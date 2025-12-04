<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Rest;

use Spryker\Glue\GlueApplication\Rest\Request\HttpRequestValidatorInterface;
use Spryker\Glue\GlueApplication\Rest\Uri\UriParserInterface;
use Spryker\Glue\Kernel\BundleControllerAction;
use Spryker\Glue\Kernel\ClassResolver\Controller\ControllerResolver;
use Spryker\Glue\Kernel\Controller\RouteNameResolver;
use Spryker\Service\Container\ContainerInterface;
use Spryker\Shared\Application\Communication\ControllerServiceBuilder;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class ResourceRouter implements ResourceRouterInterface
{
    /**
     * @var \Spryker\Glue\GlueApplication\Rest\Request\HttpRequestValidatorInterface
     */
    protected $requestHeaderValidator;

    /**
     * @var \Spryker\Service\Container\ContainerInterface
     */
    protected $application;

    /**
     * @var \Spryker\Glue\GlueApplication\Rest\Uri\UriParserInterface
     */
    protected $uriParser;

    /**
     * @var \Spryker\Glue\GlueApplication\Rest\ResourceRouteLoaderInterface
     */
    protected $resourceRouteLoader;

    /**
     * @var array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\RouterParameterExpanderPluginInterface>
     */
    protected $routerParameterExpanderPlugins;

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\HttpRequestValidatorInterface $requestHeaderValidator
     * @param \Spryker\Service\Container\ContainerInterface $application
     * @param \Spryker\Glue\GlueApplication\Rest\Uri\UriParserInterface $uriParser
     * @param \Spryker\Glue\GlueApplication\Rest\ResourceRouteLoaderInterface $resourceRouteLoader
     * @param array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\RouterParameterExpanderPluginInterface> $routerParameterExpanderPlugins
     */
    public function __construct(
        HttpRequestValidatorInterface $requestHeaderValidator,
        ContainerInterface $application,
        UriParserInterface $uriParser,
        ResourceRouteLoaderInterface $resourceRouteLoader,
        array $routerParameterExpanderPlugins
    ) {
        $this->requestHeaderValidator = $requestHeaderValidator;
        $this->application = $application;
        $this->uriParser = $uriParser;
        $this->resourceRouteLoader = $resourceRouteLoader;
        $this->routerParameterExpanderPlugins = $routerParameterExpanderPlugins;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $httpRequest
     *
     * @throws \Symfony\Component\Routing\Exception\ResourceNotFoundException
     *
     * @return array
     */
    public function matchRequest(Request $httpRequest): array
    {
        /**
         * In the `\Spryker\Glue\GlueApplication\ApiApplication\ApiApplicationProxy` we are setting this to not run into an exception.
         *
         * The `requestFlowExecutor` wasn't able to handle the request, and we have to try the API Platform. This is handled differently.
         *
         * This router can find a matching route, but the rest of the application is not able to handle.
         */
        if ($httpRequest->attributes->has('api-platform-request')) {
            throw new ResourceNotFoundException();
        }

        $resources = $this->uriParser->parse($httpRequest);
        if ($resources === null) {
            return $this->createResourceNotFoundRoute();
        }

        $resourceType = $this->getMainResource($resources);
        if ($httpRequest->getMethod() === Request::METHOD_OPTIONS) {
            return $this->createOptionsRoute($resourceType, $resources);
        }

        $route = $this->resourceRouteLoader->load(
            $resourceType[RequestConstantsInterface::ATTRIBUTE_TYPE],
            $resources,
            $httpRequest,
        );

        if (!$this->isValidRoute($route, $resources, $httpRequest)) {
            /**
             * When projects added the SymfonyFrameworkRouterPlugin we have to throw an exception here to give the Symfony Router a chance to match the route.
             *
             * For backward compatibility reasons we return the Resource Not Found route when there is no SymfonyFrameworkRouterPlugin added.
             */
            /** @var \Symfony\Cmf\Component\Routing\ChainRouterInterface|null $routerCollection */
            $routerCollection = $this->application->get('routers');

            if ($routerCollection) {
                /**
                 * The first router in this list is the GlueRouterPlugin, but we are interested in the SymfonyFrameworkRouterPlugin.
                 *
                 * We are ending up here only in case there is no matching Glue route, so we can try to match with the Symfony router.
                 *
                 * BUT: The Symfony router can also return a ResourceNotFoundException, so we have to catch it and use the original behavior of return
                 * ResourceNotFoundRoute.
                 *
                 * When the Symfony router finds a matching route, we throw the ResourceNotFoundException so that in the next loop the Symfony router can match the request.
                 */
                foreach ($routerCollection->all() as $router) {
                    if ($router instanceof Router) {
                        try {
                            // This may throw an exception which will be caught below and returns the ResourceNotFoundRoute.
                            $router->matchRequest($httpRequest);
                        } catch (ResourceNotFoundException) {
                            // Fallback behavior neither the Glue router nor the Symfony router could find a matching route.
                            return $this->createResourceNotFoundRoute();
                        }

                        // When the Symfony router found a matching route, we have to throw the ResourceNotFoundException again to give the Symfony router a chance to match the request in the next loop.
                        throw new ResourceNotFoundException();
                    }
                }
            }

            return $this->createResourceNotFoundRoute();
        }

        return $this->buildRouteParameters($route, $resourceType, $resources);
    }

    /**
     * @param string $module
     * @param string $controller
     * @param string $action
     *
     * @return array
     */
    protected function createRoute(string $module, string $controller, string $action): array
    {
        $routerResolver = new RouteNameResolver($module, $controller, $action);

        $service = (new ControllerServiceBuilder())->createServiceForController(
            $this->application,
            new BundleControllerAction($module, $controller, $action),
            new ControllerResolver(),
            $routerResolver,
        );

        return [
            '_controller' => $service,
            '_route' => $routerResolver->resolve(),
        ];
    }

    /**
     * @return array
     */
    protected function createResourceNotFoundRoute(): array
    {
        return $this->createRoute('GlueApplication', 'ErrorRest', 'resource-not-found');
    }

    /**
     * @param array $resources
     *
     * @return array
     */
    protected function getMainResource(array $resources): array
    {
        return $resources[count($resources) - 1];
    }

    /**
     * @param array $resources
     * @param array $currentResource
     *
     * @return bool
     */
    protected function isValidPath(array $resources, array $currentResource): bool
    {
        foreach ($resources as $resourceNr => $resource) {
            if ($resource[RequestConstantsInterface::ATTRIBUTE_TYPE] !== $currentResource[RequestConstantsInterface::ATTRIBUTE_PARENT_RESOURCE]) {
                continue;
            }

            return $this->isValidChildParent($resources, $currentResource, $resourceNr);
        }

        return false;
    }

    /**
     * @param array $resources
     * @param array $currentResource
     * @param int $resourceNr
     *
     * @return bool
     */
    protected function isValidChildParent(array $resources, array $currentResource, int $resourceNr): bool
    {
        $nextResource = $resources[$resourceNr + 1] ?? null;
        if (!$nextResource) {
            return false;
        }

        if ($nextResource[RequestConstantsInterface::ATTRIBUTE_TYPE] === $currentResource[RequestConstantsInterface::ATTRIBUTE_TYPE]) {
            return true;
        }

        return false;
    }

    /**
     * @param array $route
     * @param array $resource
     * @param array $resources
     *
     * @return array
     */
    protected function buildRouteParameters(array $route, array $resource, array $resources): array
    {
        $routeParams = $this->createRoute(
            $route[RequestConstantsInterface::ATTRIBUTE_MODULE],
            $route[RequestConstantsInterface::ATTRIBUTE_CONTROLLER],
            $route[RequestConstantsInterface::ATTRIBUTE_CONFIGURATION]['action'],
        );

        $routeParams = array_merge(
            $routeParams,
            $resource,
            [
                RequestConstantsInterface::ATTRIBUTE_ALL_RESOURCES => $resources,
                RequestConstantsInterface::ATTRIBUTE_RESOURCE_FQCN => $route[RequestConstantsInterface::ATTRIBUTE_RESOURCE_FQCN],
                RequestConstantsInterface::ATTRIBUTE_CONTEXT => $route[RequestConstantsInterface::ATTRIBUTE_CONFIGURATION]['context'],
                RequestConstantsInterface::ATTRIBUTE_IS_PROTECTED => $route[RequestConstantsInterface::ATTRIBUTE_CONFIGURATION]['is_protected'],
            ],
        );

        foreach ($this->routerParameterExpanderPlugins as $routerParameterExpanderPlugin) {
            $routeParams = $routerParameterExpanderPlugin->expandRouteParameters($route, $routeParams);
        }

        return $routeParams;
    }

    /**
     * @param array $resourceType
     * @param array $resources
     *
     * @return array
     */
    protected function createOptionsRoute(array $resourceType, array $resources): array
    {
        $route = $this->createRoute('GlueApplication', 'Options', 'resource-options');
        $route[RequestConstantsInterface::ATTRIBUTE_TYPE] = $resourceType[RequestConstantsInterface::ATTRIBUTE_TYPE];
        $route[RequestConstantsInterface::ATTRIBUTE_ALL_RESOURCES] = $resources;

        return $route;
    }

    /**
     * @param array $route
     * @param array $resources
     *
     * @return bool
     */
    protected function isParentValid(array $route, array $resources): bool
    {
        if (isset($route[RequestConstantsInterface::ATTRIBUTE_PARENT_RESOURCE]) && count($resources) > 1) {
            if ($route[RequestConstantsInterface::ATTRIBUTE_PARENT_RESOURCE] !== $resources[0][RequestConstantsInterface::ATTRIBUTE_TYPE]) {
                return false;
            }

            return $this->isValidPath($resources, $route);
        }

        return $route[RequestConstantsInterface::ATTRIBUTE_TYPE] === $resources[0][RequestConstantsInterface::ATTRIBUTE_TYPE];
    }

    /**
     * @param array|null $route
     * @param array $resources
     * @param \Symfony\Component\HttpFoundation\Request $httpRequest
     *
     * @return bool
     */
    protected function isValidRoute(?array $route, array $resources, Request $httpRequest): bool
    {
        if (!$route || !$this->isParentValid($route, $resources)) {
            return false;
        }

        if ($httpRequest->getMethod() === Request::METHOD_POST && isset($this->getMainResource($resources)['id'])) {
            return false;
        }

        return true;
    }
}
