<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\ApiApplication;

use Closure;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueRequestValidationTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\GlueApplication\ApiApplication\Type\RequestFlowAwareApiApplication;
use Spryker\Glue\GlueApplication\Builder\RequestBuilderInterface;
use Spryker\Glue\GlueApplication\Executor\ResourceExecutorInterface;
use Spryker\Glue\GlueApplication\Formatter\ResponseFormatterInterface;
use Spryker\Glue\GlueApplication\Router\RouteMatcherInterface;
use Spryker\Glue\GlueApplication\Validator\RequestValidatorInterface;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ConventionPluginInterface;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\MissingResourceInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestFlowExecutor implements RequestFlowExecutorInterface
{
    /**
     * @param \Spryker\Glue\GlueApplication\Executor\ResourceExecutorInterface $resourceExecutor
     * @param \Spryker\Glue\GlueApplication\Router\RouteMatcherInterface $routeMatcher
     * @param \Spryker\Glue\GlueApplication\Builder\RequestBuilderInterface $requestBuilder
     * @param \Spryker\Glue\GlueApplication\Validator\RequestValidatorInterface $requestValidator
     * @param \Spryker\Glue\GlueApplication\Formatter\ResponseFormatterInterface $responseFormatter
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(
        protected ResourceExecutorInterface $resourceExecutor,
        protected RouteMatcherInterface $routeMatcher,
        protected RequestBuilderInterface $requestBuilder,
        protected RequestValidatorInterface $requestValidator,
        protected ResponseFormatterInterface $responseFormatter,
        protected Request $request
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     * @param \Spryker\Glue\GlueApplication\ApiApplication\Type\RequestFlowAwareApiApplication $apiApplication
     * @param \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ConventionPluginInterface|null $conventionPlugin
     *
     * @return \Generated\Shared\Transfer\GlueResponseTransfer
     */
    public function executeRequestFlow(
        GlueRequestTransfer $glueRequestTransfer,
        RequestFlowAwareApiApplication $apiApplication,
        ?ConventionPluginInterface $conventionPlugin = null
    ): GlueResponseTransfer {
        $glueRequestTransfer = $this->requestBuilder->build(
            $glueRequestTransfer,
            $apiApplication,
            $conventionPlugin,
        );

        $glueRequestValidationTransfer = $this->requestValidator->validate(
            $glueRequestTransfer,
            $apiApplication,
            $conventionPlugin,
        );
        if ($glueRequestValidationTransfer->getIsValid() === false) {
            return $this->sendValidationErrorResponse($glueRequestTransfer, $glueRequestValidationTransfer, $apiApplication, $conventionPlugin);
        }

        $resource = $this->routeMatcher->route($glueRequestTransfer);
        if ($resource instanceof MissingResourceInterface) {
            return $this->sendMissingResourceResponse($glueRequestTransfer, $resource, $apiApplication, $conventionPlugin);
        }

        $glueRequestValidationTransfer = $this->requestValidator->validateAfterRouting(
            $glueRequestTransfer,
            $resource,
            $apiApplication,
            $conventionPlugin,
        );

        if (!$glueRequestValidationTransfer->getIsValid()) {
            return $this->sendValidationErrorResponse($glueRequestTransfer, $glueRequestValidationTransfer, $apiApplication, $conventionPlugin);
        }

        $executableResource = $resource->getResource($glueRequestTransfer);
        if (!$executableResource instanceof Closure) {
            $this->request->attributes->add($glueRequestTransfer->getResource()->getParameters());
            $apiApplication->dispatchControllerEvent($this->request, $resource->getResource($glueRequestTransfer));
        }

        $glueResponseTransfer = $this->resourceExecutor->executeResource($resource, $glueRequestTransfer);
        $glueResponseTransfer->setHasExecutableResource(true);

        return $this->responseFormatter->format(
            $glueResponseTransfer,
            $glueRequestTransfer,
            $apiApplication,
            $conventionPlugin,
            $resource,
        );
    }

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     * @param \Generated\Shared\Transfer\GlueRequestValidationTransfer $glueRequestValidationTransfer
     * @param \Spryker\Glue\GlueApplication\ApiApplication\Type\RequestFlowAwareApiApplication $apiApplication
     * @param \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ConventionPluginInterface|null $apiConvention
     *
     * @return \Generated\Shared\Transfer\GlueResponseTransfer
     */
    protected function sendValidationErrorResponse(
        GlueRequestTransfer $glueRequestTransfer,
        GlueRequestValidationTransfer $glueRequestValidationTransfer,
        RequestFlowAwareApiApplication $apiApplication,
        ?ConventionPluginInterface $apiConvention = null
    ): GlueResponseTransfer {
        $glueResponseTransfer = (new GlueResponseTransfer())
            ->setHttpStatus($glueRequestValidationTransfer->getStatus())
            ->setErrors($glueRequestValidationTransfer->getErrors());

        return $this->responseFormatter->format(
            $glueResponseTransfer,
            $glueRequestTransfer,
            $apiApplication,
            $apiConvention,
        );
    }

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     * @param \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\MissingResourceInterface $missingResource
     * @param \Spryker\Glue\GlueApplication\ApiApplication\Type\RequestFlowAwareApiApplication $apiApplication
     * @param \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ConventionPluginInterface|null $apiConvention
     *
     * @return \Generated\Shared\Transfer\GlueResponseTransfer
     */
    protected function sendMissingResourceResponse(
        GlueRequestTransfer $glueRequestTransfer,
        MissingResourceInterface $missingResource,
        RequestFlowAwareApiApplication $apiApplication,
        ?ConventionPluginInterface $apiConvention
    ): GlueResponseTransfer {
        $glueResponseTransfer = $this->resourceExecutor->executeResource($missingResource, $glueRequestTransfer);

        return $this->responseFormatter->format(
            $glueResponseTransfer,
            $glueRequestTransfer,
            $apiApplication,
            $apiConvention,
        );
    }
}
