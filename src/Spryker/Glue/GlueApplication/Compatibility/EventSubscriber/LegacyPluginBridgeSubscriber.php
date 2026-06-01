<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\GlueApplication\Compatibility\EventSubscriber;

use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\RestErrorMessageTransfer;
use Spryker\ApiPlatform\Exception\GlueApiException;
use Spryker\Glue\GlueApplication\Compatibility\RequestBuilder\SyntheticRestRequestBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponse;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ControllerBeforeActionPluginInterface;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\RestRequestValidatorPluginInterface;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\RestUserValidatorPluginInterface;
use Spryker\Service\Container\Attributes\Plugins;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

/**
 * Compatibility bridge that runs the legacy `getControllerBeforeActionPlugins`,
 * `getRestUserValidatorPlugins`, `getRestRequestValidatorPlugins`, and
 * `getControllerAfterActionPlugins` chains for endpoints served by API Platform.
 *
 * The legacy `RequestFlowExecutor` short-circuits with a missing-resource response
 * once a route is unwired from `getResourceRoutePlugins`, so the post-routing
 * plugin stacks never run for migrated endpoints. This subscriber re-attaches
 * those stacks to the API Platform request lifecycle, which means a project that
 * unwires a `*ResourceRoutePlugin` keeps every cross-cutting plugin (MFA,
 * Authorization, SecurityBlocker, project-level customizations) active without
 * any further migration work.
 *
 * Removed when API Platform exposes its own extension-point plugins for
 * cross-cutting concerns; until then this is the migration shim.
 */
class LegacyPluginBridgeSubscriber implements EventSubscriberInterface
{
    /**
     * Higher than the routing pass (32) so the bridge can short-circuit with a
     * 4xx before API Platform Provider/Processor logic fires. Lower than the
     * customer/company-user identity subscribers (priority 6/5) so we already
     * have `CustomerTransfer` on the request.
     */
    protected const int PRIORITY = 4;

    protected const string ATTRIBUTE_CUSTOMER_TRANSFER = 'CustomerTransfer';

    protected const string ATTRIBUTE_API_RESOURCE_CLASS = '_api_resource_class';

    protected const string ATTRIBUTE_API_OPERATION_NAME = '_api_operation_name';

    /**
     * Mirrors the `$action` argument the legacy `ControllerFilter` passed into
     * `beforeAction()` — kept blank to signal "non-controller dispatch path".
     */
    protected const string DELEGATED_ACTION_NAME = '';

    protected const string ATTRIBUTE_SYNTHETIC_REST_REQUEST = '_bridge_synthetic_rest_request';

    protected const string JSON_KEY_ERRORS = 'errors';

    protected const string JSON_KEY_CODE = 'code';

    protected const string JSON_KEY_DETAIL = 'detail';

    protected const string JSON_KEY_STATUS = 'status';

    protected const string EXTRA_PROPERTY_ATTRIBUTES_CLASS = 'resourceAttributesClassName';

    /**
     * @param array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\RestUserValidatorPluginInterface> $restUserValidatorPlugins
     * @param array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ControllerBeforeActionPluginInterface> $controllerBeforeActionPlugins
     * @param array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\RestRequestValidatorPluginInterface> $restRequestValidatorPlugins
     * @param array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ControllerAfterActionPluginInterface> $controllerAfterActionPlugins
     */
    public function __construct(
        protected ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory,
        protected SyntheticRestRequestBuilderInterface $syntheticRestRequestBuilder,
        #[Plugins(dependencyProviderMethod: 'getRestUserValidatorPlugins')]
        protected array $restUserValidatorPlugins = [],
        #[Plugins(dependencyProviderMethod: 'getControllerBeforeActionPlugins')]
        protected array $controllerBeforeActionPlugins = [],
        #[Plugins(dependencyProviderMethod: 'getRestRequestValidatorPlugins')]
        protected array $restRequestValidatorPlugins = [],
        #[Plugins(dependencyProviderMethod: 'getControllerAfterActionPlugins')]
        protected array $controllerAfterActionPlugins = [],
    ) {
    }

    /**
     * @return array<string, array{string, int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', static::PRIORITY],
            KernelEvents::RESPONSE => ['onKernelResponse', 0],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!$this->isApiPlatformRequest($request)) {
            return;
        }

        $resourceShortName = $this->resolveResourceShortName($request);

        if ($resourceShortName === null) {
            return;
        }

        if ($this->restUserValidatorPlugins === [] && $this->controllerBeforeActionPlugins === [] && $this->restRequestValidatorPlugins === []) {
            return;
        }

        $customerTransfer = $this->resolveCustomerTransfer($request);
        $attributesClass = $this->resolveAttributesClass($request);

        $restRequest = $this->syntheticRestRequestBuilder->build($request, $customerTransfer, $resourceShortName, $attributesClass);

        $request->attributes->set(static::ATTRIBUTE_SYNTHETIC_REST_REQUEST, $restRequest);

        foreach ($this->restRequestValidatorPlugins as $restRequestValidatorPlugin) {
            $this->runRestRequestValidatorPlugin($restRequestValidatorPlugin, $request, $restRequest);
        }

        // The bridge only replays the customer-flow plugins (MFA, Authorization,
        // SetCustomer, etc.). Agent and anonymous endpoints have no CustomerTransfer
        // on the request and would be wrongly rejected by customer-context validators.
        if ($customerTransfer === null) {
            return;
        }

        foreach ($this->controllerBeforeActionPlugins as $controllerBeforeActionPlugin) {
            $this->runControllerBeforeActionPlugin($controllerBeforeActionPlugin, $restRequest);
        }

        foreach ($this->restUserValidatorPlugins as $restUserValidatorPlugin) {
            $this->runRestUserValidatorPlugin($restUserValidatorPlugin, $restRequest);
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest() || $this->controllerAfterActionPlugins === []) {
            return;
        }

        $request = $event->getRequest();
        $restRequest = $request->attributes->get(static::ATTRIBUTE_SYNTHETIC_REST_REQUEST);

        if (!$restRequest instanceof RestRequestInterface) {
            return;
        }

        $restResponse = $this->buildSyntheticRestResponse($event->getResponse());

        foreach ($this->controllerAfterActionPlugins as $controllerAfterActionPlugin) {
            $controllerAfterActionPlugin->afterAction(static::DELEGATED_ACTION_NAME, $restRequest, $restResponse);
        }
    }

    protected function buildSyntheticRestResponse(Response $httpResponse): RestResponseInterface
    {
        $restResponse = new RestResponse();
        $body = json_decode((string)$httpResponse->getContent(), true);

        if (!is_array($body) || !isset($body[static::JSON_KEY_ERRORS])) {
            return $restResponse;
        }

        foreach ((array)$body[static::JSON_KEY_ERRORS] as $error) {
            $restResponse->addError(
                (new RestErrorMessageTransfer())
                    ->setCode((string)($error[static::JSON_KEY_CODE] ?? ''))
                    ->setDetail((string)($error[static::JSON_KEY_DETAIL] ?? ''))
                    ->setStatus((int)($error[static::JSON_KEY_STATUS] ?? 0)),
            );
        }

        return $restResponse;
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function runRestUserValidatorPlugin(
        RestUserValidatorPluginInterface $plugin,
        RestRequestInterface $restRequest,
    ): void {
        $restErrorMessageTransfer = $plugin->validate($restRequest);

        if ($restErrorMessageTransfer === null) {
            return;
        }

        throw new GlueApiException(
            (int)($restErrorMessageTransfer->getStatus() ?? Response::HTTP_FORBIDDEN),
            (string)($restErrorMessageTransfer->getCode() ?? ''),
            (string)($restErrorMessageTransfer->getDetail() ?? ''),
        );
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function runRestRequestValidatorPlugin(
        RestRequestValidatorPluginInterface $plugin,
        Request $httpRequest,
        RestRequestInterface $restRequest,
    ): void {
        $restErrorCollectionTransfer = $plugin->validate($httpRequest, $restRequest);

        if ($restErrorCollectionTransfer === null || $restErrorCollectionTransfer->getRestErrors()->count() === 0) {
            return;
        }

        $restError = $restErrorCollectionTransfer->getRestErrors()->getIterator()->current();

        throw new GlueApiException(
            (int)($restError->getStatus() ?? Response::HTTP_UNPROCESSABLE_ENTITY),
            (string)($restError->getCode() ?? ''),
            (string)($restError->getDetail() ?? ''),
        );
    }

    protected function runControllerBeforeActionPlugin(
        ControllerBeforeActionPluginInterface $plugin,
        RestRequestInterface $restRequest,
    ): void {
        try {
            $plugin->beforeAction(static::DELEGATED_ACTION_NAME, $restRequest);
        } catch (Throwable $throwable) {
            // Legacy `beforeAction()` plugins are typed `void` and only mutate state, but project
            // implementations may throw. Swallow non-fatal errors so a misbehaving customer plugin
            // never breaks API Platform request handling — the plugin's intended side-effects
            // simply don't happen for that request.
        }
    }

    protected function isApiPlatformRequest(Request $request): bool
    {
        return $request->attributes->has(static::ATTRIBUTE_API_RESOURCE_CLASS);
    }

    protected function resolveResourceShortName(Request $request): ?string
    {
        $resourceClass = (string)$request->attributes->get(static::ATTRIBUTE_API_RESOURCE_CLASS, '');
        $operationName = (string)$request->attributes->get(static::ATTRIBUTE_API_OPERATION_NAME, '');

        if ($resourceClass === '' || !class_exists($resourceClass)) {
            return null;
        }

        try {
            $operation = $this->resourceMetadataCollectionFactory
                ->create($resourceClass)
                ->getOperation($operationName);
        } catch (Throwable) {
            return null;
        }

        $shortName = $operation->getShortName();

        return $shortName !== null && $shortName !== '' ? $shortName : null;
    }

    protected function resolveAttributesClass(Request $request): ?string
    {
        $resourceClass = (string)$request->attributes->get(static::ATTRIBUTE_API_RESOURCE_CLASS, '');
        $operationName = (string)$request->attributes->get(static::ATTRIBUTE_API_OPERATION_NAME, '');

        if ($resourceClass === '' || !class_exists($resourceClass)) {
            return null;
        }

        try {
            $operation = $this->resourceMetadataCollectionFactory
                ->create($resourceClass)
                ->getOperation($operationName);
        } catch (Throwable) {
            return null;
        }

        $extraProperties = $operation->getExtraProperties();
        $class = $extraProperties[static::EXTRA_PROPERTY_ATTRIBUTES_CLASS] ?? null;

        return is_string($class) && $class !== '' ? $class : null;
    }

    protected function resolveCustomerTransfer(Request $request): ?CustomerTransfer
    {
        $customerTransfer = $request->attributes->get(static::ATTRIBUTE_CUSTOMER_TRANSFER);

        return $customerTransfer instanceof CustomerTransfer ? $customerTransfer : null;
    }
}
