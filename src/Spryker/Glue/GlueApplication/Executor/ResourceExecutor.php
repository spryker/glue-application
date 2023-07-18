<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Executor;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\GlueApplication\Cache\Reader\ControllerCacheReaderInterface;
use Spryker\Glue\GlueApplication\Cache\Writer\ControllerCacheWriterInterface;
use Spryker\Glue\GlueApplication\Exception\InvalidActionParametersException;
use Spryker\Glue\GlueApplication\GlueApplicationConfig;
use Spryker\Glue\GlueApplication\Resource\GenericResource;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Symfony\Component\HttpFoundation\Request;

class ResourceExecutor implements ResourceExecutorInterface
{
    /**
     * @var string
     */
    protected const CLEAR_CACHE_ERROR_MESSAGE = 'Method with requested parameters is not found.
                Run `glue glue-api:controller:cache:warm-up` to update a controller cache.';

    /**
     * @var \Spryker\Glue\GlueApplication\Cache\Reader\ControllerCacheReaderInterface
     */
    protected $controllerCacheReader;

    /**
     * @var \Spryker\Glue\GlueApplication\Cache\Writer\ControllerCacheWriterInterface
     */
    protected $controllerCacheWriter;

    /**
     * @var \Spryker\Glue\GlueApplication\GlueApplicationConfig
     */
    protected $glueApplicationConfig;

    /**
     * @param \Spryker\Glue\GlueApplication\Cache\Reader\ControllerCacheReaderInterface $controllerCacheReader
     * @param \Spryker\Glue\GlueApplication\Cache\Writer\ControllerCacheWriterInterface $controllerCacheWriter
     * @param \Spryker\Glue\GlueApplication\GlueApplicationConfig $glueApplicationConfig
     */
    public function __construct(
        ControllerCacheReaderInterface $controllerCacheReader,
        ControllerCacheWriterInterface $controllerCacheWriter,
        GlueApplicationConfig $glueApplicationConfig
    ) {
        $this->controllerCacheReader = $controllerCacheReader;
        $this->controllerCacheWriter = $controllerCacheWriter;
        $this->glueApplicationConfig = $glueApplicationConfig;
    }

    /**
     * @param \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface $resource
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @throws \Spryker\Glue\GlueApplication\Exception\InvalidActionParametersException
     *
     * @return \Generated\Shared\Transfer\GlueResponseTransfer
     */
    public function executeResource(
        ResourceInterface $resource,
        GlueRequestTransfer $glueRequestTransfer
    ): GlueResponseTransfer {
        if ($this->glueApplicationConfig->isDevelopmentMode()) {
            $this->controllerCacheWriter->cache($glueRequestTransfer->getApplication());
        }

        $executableResource = $resource->getResource($glueRequestTransfer);

        $parameters = $this->controllerCacheReader->getActionParameters($executableResource, $resource, $glueRequestTransfer);
        if ($parameters === null) {
            throw new InvalidActionParametersException(static::CLEAR_CACHE_ERROR_MESSAGE);
        }

        if ($glueRequestTransfer->getContent()) {
            $attributesTransfer = $this->getAttributesTransfer($resource, $glueRequestTransfer, $parameters);

            if (!$attributesTransfer) {
                return call_user_func_array(
                    $executableResource,
                    $this->collectParameters($parameters, [$glueRequestTransfer]),
                );
            }

            $attributesTransfer->fromArray($glueRequestTransfer->getAttributes(), true);
            $glueRequestTransfer->getResource()->setAttributes($attributesTransfer);

            return call_user_func_array(
                $executableResource,
                $this->collectParameters(
                    $parameters,
                    [$attributesTransfer, $glueRequestTransfer],
                ),
            );
        }

        if ($glueRequestTransfer->getResource() && $glueRequestTransfer->getResource()->getId()) {
            return call_user_func_array(
                $executableResource,
                $this->collectParameters(
                    $parameters,
                    [$glueRequestTransfer->getResource()->getId(), $glueRequestTransfer],
                ),
            );
        }

        return call_user_func_array(
            $executableResource,
            $this->collectParameters($parameters, [$glueRequestTransfer]),
        );
    }

    /**
     * @param \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface $resource
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     * @param array<mixed> $parameters
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer|null
     */
    protected function getAttributesTransfer(
        ResourceInterface $resource,
        GlueRequestTransfer $glueRequestTransfer,
        array $parameters
    ): ?AbstractTransfer {
        if ($resource instanceof GenericResource) {
            foreach ($parameters as $parameterType => $parameter) {
                if (
                    class_exists($parameterType)
                    && is_subclass_of($parameterType, AbstractTransfer::class)
                    && !$parameterType instanceof GlueRequestTransfer
                ) {
                    return new $parameterType();
                }
            }
        }

        $glueResourceMethodCollectionTransfer = $resource->getDeclaredMethods();

        $method = strtolower($glueRequestTransfer->getResource()->getMethod());
        if (!$glueResourceMethodCollectionTransfer->offsetExists($method)) {
            return null;
        }

        /** @var \Generated\Shared\Transfer\GlueResourceMethodConfigurationTransfer|null $glueResourceMethodConfigurationTransfer */
        $glueResourceMethodConfigurationTransfer = $glueResourceMethodCollectionTransfer
            ->offsetGet(strtolower($glueRequestTransfer->getResource()->getMethod()));

        if ($glueResourceMethodConfigurationTransfer && $glueResourceMethodConfigurationTransfer->getAttributes()) {
            $attributeTransfer = $glueResourceMethodConfigurationTransfer->getAttributesOrFail();
            if (
                is_subclass_of($attributeTransfer, AbstractTransfer::class) &&
                !$attributeTransfer instanceof GlueRequestTransfer
            ) {
                return new $attributeTransfer();
            }
        }

        foreach ($parameters as $parameterType => $parameter) {
            if (
                class_exists($parameterType)
                && is_subclass_of($parameterType, AbstractTransfer::class)
                && !$parameterType instanceof GlueRequestTransfer
            ) {
                return new $parameterType();
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $parameters
     * @param array<mixed> $options
     *
     * @return array<mixed>
     */
    protected function collectParameters(array $parameters, array $options): array
    {
        $parameters = $this->setDefaultRequest($parameters);

        foreach ($options as $option) {
            if (is_object($option) && isset($parameters[get_class($option)])) {
                $parameters[get_class($option)] = $option;

                continue;
            }

            if (isset($parameters[getType($option)])) {
                $parameters[getType($option)] = $option;
            }
        }

        $parameters[GlueResponseTransfer::class] = new GlueResponseTransfer();

        return array_values($parameters);
    }

    /**
     * @param array<string, mixed> $parameters
     *
     * @return array<string, mixed>
     */
    protected function setDefaultRequest(array $parameters): array
    {
        if (array_key_exists(Request::class, $parameters)) {
            $parameters[Request::class] = Request::createFromGlobals();
        }

        return $parameters;
    }
}
