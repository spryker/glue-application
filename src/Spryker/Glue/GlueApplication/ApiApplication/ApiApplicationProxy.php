<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\ApiApplication;

use Generated\Shared\Transfer\GlueErrorTransfer;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Spryker\Glue\GlueApplication\ApiApplication\Type\RequestFlowAgnosticApiApplication;
use Spryker\Glue\GlueApplication\ApiApplication\Type\RequestFlowAwareApiApplication;
use Spryker\Glue\GlueApplication\ContentNegotiator\ContentNegotiatorInterface;
use Spryker\Glue\GlueApplication\Exception\UnknownRequestFlowImplementationException;
use Spryker\Glue\GlueApplication\GlueApplicationConfig;
use Spryker\Glue\GlueApplication\Http\Request\RequestBuilderInterface;
use Spryker\Glue\GlueApplication\Http\Response\HttpSenderInterface;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\CommunicationProtocolPluginInterface;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ConventionPluginInterface;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\GlueApplicationBootstrapPluginInterface;
use Spryker\Service\Container\ContainerInterface;
use Spryker\Shared\Application\ApplicationInterface;
use Spryker\Shared\Application\Kernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\TerminableInterface;
use Throwable;

class ApiApplicationProxy implements ApplicationInterface
{
    /**
     * @var \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\GlueApplicationBootstrapPluginInterface
     */
    protected GlueApplicationBootstrapPluginInterface $glueApplicationBootstrapPlugin;

    /**
     * @var \Spryker\Glue\GlueApplication\ApiApplication\RequestFlowExecutorInterface
     */
    protected RequestFlowExecutorInterface $requestFlowExecutor;

    /**
     * @var array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\CommunicationProtocolPluginInterface>
     */
    protected array $communicationProtocolPlugins;

    /**
     * @var array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ConventionPluginInterface>
     */
    protected array $conventionPlugins;

    /**
     * @var \Spryker\Glue\GlueApplication\Http\Request\RequestBuilderInterface
     */
    protected RequestBuilderInterface $requestBuilder;

    /**
     * @var \Spryker\Glue\GlueApplication\Http\Response\HttpSenderInterface
     */
    protected HttpSenderInterface $httpSender;

    /**
     * @var \Spryker\Glue\GlueApplication\ContentNegotiator\ContentNegotiatorInterface
     */
    protected ContentNegotiatorInterface $contentNegotiator;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected Request $request;

    /**
     * @var \Spryker\Glue\GlueApplication\GlueApplicationConfig
     */
    protected GlueApplicationConfig $config;

    protected ?PsrContainerInterface $container = null;

    /**
     * @param \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\GlueApplicationBootstrapPluginInterface $glueApplicationBootstrapPlugin
     * @param \Spryker\Glue\GlueApplication\ApiApplication\RequestFlowExecutorInterface $requestFlowExecutor
     * @param array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\CommunicationProtocolPluginInterface> $communicationProtocolPlugins
     * @param array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ConventionPluginInterface> $apiConventionPlugins
     * @param \Spryker\Glue\GlueApplication\Http\Request\RequestBuilderInterface $requestBuilder
     * @param \Spryker\Glue\GlueApplication\Http\Response\HttpSenderInterface $httpSender
     * @param \Spryker\Glue\GlueApplication\ContentNegotiator\ContentNegotiatorInterface $contentNegotiator
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Spryker\Glue\GlueApplication\GlueApplicationConfig $glueApplicationConfig
     */
    public function __construct(
        GlueApplicationBootstrapPluginInterface $glueApplicationBootstrapPlugin,
        RequestFlowExecutorInterface $requestFlowExecutor,
        array $communicationProtocolPlugins,
        array $apiConventionPlugins,
        RequestBuilderInterface $requestBuilder,
        HttpSenderInterface $httpSender,
        ContentNegotiatorInterface $contentNegotiator,
        Request $request,
        GlueApplicationConfig $glueApplicationConfig
    ) {
        $this->glueApplicationBootstrapPlugin = $glueApplicationBootstrapPlugin;
        $this->communicationProtocolPlugins = $communicationProtocolPlugins;
        $this->requestFlowExecutor = $requestFlowExecutor;
        $this->conventionPlugins = $apiConventionPlugins;
        $this->requestBuilder = $requestBuilder;
        $this->httpSender = $httpSender;
        $this->contentNegotiator = $contentNegotiator;
        $this->request = $request;
        $this->config = $glueApplicationConfig;
    }

    public function setContainer(PsrContainerInterface $container): ApplicationInterface
    {
        $this->container = $container;

        return $this;
    }

    public function getContainer(): PsrContainerInterface
    {
        return $this->container;
    }

    /**
     * @return \Spryker\Shared\Application\ApplicationInterface
     */
    public function boot(): ApplicationInterface
    {
        $application = $this->glueApplicationBootstrapPlugin->getApplication();
        $application->boot();

        return $this;
    }

    /**
     * @throws \Spryker\Glue\GlueApplication\Exception\UnknownRequestFlowImplementationException
     *
     * @return void
     */
    public function run(): void
    {
        $bootstrapApplication = $this->glueApplicationBootstrapPlugin->getApplication();

        if ($bootstrapApplication instanceof RequestFlowAgnosticApiApplication) {
            $bootstrapApplication->run();

            return;
        }

        /** @phpstan-var \Spryker\Service\Container\ContainerInterface $container */
        $container = $bootstrapApplication->getContainer();

        if ($container instanceof ContainerInterface) {
            $kernel = new Kernel($container);
            $kernel->setApplication($bootstrapApplication);
            $kernel->boot();
        }

        if ($bootstrapApplication instanceof RequestFlowAwareApiApplication) {
            $communicationProtocolPlugin = $this->resolveCommunicationPlugin($this->communicationProtocolPlugins);
            $glueRequestTransfer = $this->extractRequest($communicationProtocolPlugin);
            $glueRequestTransfer = $this->contentNegotiator->negotiate($glueRequestTransfer);

            $apiConventionPlugin = null;
            if ($glueRequestTransfer->getConvention() !== null) {
                $apiConventionPlugin = $this->resolveConvention($this->conventionPlugins, $glueRequestTransfer);
            }

            $glueResponseTransfer = $this->requestFlowExecutor->executeRequestFlow(
                $glueRequestTransfer,
                $bootstrapApplication,
                $apiConventionPlugin,
            );

            /**
             * @deprecated Will be removed in the next major. Exists for BC-reason only.
             */
            if ($communicationProtocolPlugin !== null) {
                $communicationProtocolPlugin->sendResponse($glueResponseTransfer);

                return;
            }

            /**
             * In case we get a 404 not found from Glue Application, we fall back to the Symfony kernel to
             * allow other Symfony applications (e.g., Backoffice, Frontend) to handle the request.
             *
             * This is needed to be able to use the API Platform and Glue Application in the same project.
             *
             * In case the Glue router was able to find a resource class it can execute, but it returned a 404, we must skip the API-Platform handling.
             *
             * For the Glue APIs we add resources in either of:
             * - `\Pyz\Glue\GlueStorefrontApiApplication\GlueStorefrontApiApplicationDependencyProvider::getResourcePlugins()`
             * - `\Pyz\Glue\GlueBackendApiApplication\GlueBackendApiApplicationDependencyProvider::getResourcePlugins()`
             *
             * Using API-Platform together with Glue Application is not supported when the resource is available in the Glue Application.
             */
            if (($glueResponseTransfer->getHttpStatus() === Response::HTTP_NOT_FOUND && $glueResponseTransfer->getHasExecutableResource() !== true) || $glueResponseTransfer->getHttpStatus() === Response::HTTP_UNSUPPORTED_MEDIA_TYPE) {
                /**
                 * We need to catch exceptions here to prevent migration exceptions.
                 *
                 * When the request was not handleable by the `requestFlowExecutor` e.g., because of this is an API Platform request with
                 * an in the Glue Application unsupported accept header where the `requestFlowExecutor` validation would fail, then we try the API-Platform request.
                 *
                 * In case of the resource is still in the Glue application available, the router would resolve to the respective ResourceController which then fails because of
                 * missing GlueRequestTransfer in the
                 */
                try {
                    $this->request->attributes->set('api-platform-request', true);

                    $response = $kernel->handle($this->request);
                    $glueResponseTransfer->setHttpStatus($response->getStatusCode());
                    $glueResponseTransfer->setContent((string)$response->getContent());
                    $glueResponseTransfer->setMeta($response->headers->all());

                    $glueResponseTransfer->setFormat(null);
                } catch (Throwable $throwable) {
                    $glueResponseTransfer->addError((new GlueErrorTransfer())->setMessage('Tried to use API Platform to handle the request, but it failed with: ' . $throwable->getMessage()));
                }
            }

            $response = $this->httpSender->sendResponse($glueResponseTransfer, $this->request, $bootstrapApplication);

            $this->terminateApplication(
                $bootstrapApplication,
                $response,
            );

            return;
        }

        throw new UnknownRequestFlowImplementationException(sprintf(
            '%s needs to implement either %s or %s',
            get_class($bootstrapApplication),
            RequestFlowAgnosticApiApplication::class,
            RequestFlowAwareApiApplication::class,
        ));
    }

    /**
     * @param \Spryker\Glue\GlueApplication\ApiApplication\Type\RequestFlowAwareApiApplication $bootstrapApplication
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return void
     */
    protected function terminateApplication(RequestFlowAwareApiApplication $bootstrapApplication, Response $response): void
    {
        if (!$bootstrapApplication instanceof TerminableInterface) {
            return;
        }

        if (!$this->config->isTerminationEnabled()) {
            return;
        }

        $bootstrapApplication->terminate($this->request, $response);
    }

    /**
     * @param array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ConventionPluginInterface> $apiConventionPlugins
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ConventionPluginInterface|null
     */
    protected function resolveConvention(array $apiConventionPlugins, GlueRequestTransfer $glueRequestTransfer): ?ConventionPluginInterface
    {
        foreach ($apiConventionPlugins as $apiConventionPlugin) {
            if ($apiConventionPlugin->getName() === $glueRequestTransfer->getConvention()) {
                return $apiConventionPlugin;
            }
        }

        return null;
    }

    /**
     * @deprecated Will be removed in the next major. Exists for BC-reason only.
     *
     * @param array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\CommunicationProtocolPluginInterface> $communicationProtocolPlugins
     *
     * @return \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\CommunicationProtocolPluginInterface|null
     */
    protected function resolveCommunicationPlugin(array $communicationProtocolPlugins): ?CommunicationProtocolPluginInterface
    {
        foreach ($communicationProtocolPlugins as $communicationProtocolPlugin) {
            if ($communicationProtocolPlugin->isApplicable()) {
                return $communicationProtocolPlugin;
            }
        }

        return null;
    }

    /**
     * @param \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\CommunicationProtocolPluginInterface|null $communicationProtocolPlugin
     *
     * @return \Generated\Shared\Transfer\GlueRequestTransfer
     */
    protected function extractRequest(?CommunicationProtocolPluginInterface $communicationProtocolPlugin): GlueRequestTransfer
    {
        if ($communicationProtocolPlugin !== null) {
            return $communicationProtocolPlugin->extractRequest(new GlueRequestTransfer());
        }

        return $this->requestBuilder->extract(new GlueRequestTransfer());
    }
}
