<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Context;


use Spryker\Glue\GlueApplication\GlueApplicationDependencyProvider;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ApiApplicationContextPluginInterface;
use Spryker\Glue\Kernel\Container;

class ApiApplicationContextDependencyResolver
{
    /**
     * @var \Spryker\Glue\Kernel\Container
     */
    protected $container;

    /**
     * @var \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ApiApplicationContextPluginInterface|null
     */
    protected $apiApplicationContextPlugin;

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     * @param \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ApiApplicationContextPluginInterface[] $apiApplicationContextPlugins
     */
    public function __construct(Container $container, array $apiApplicationContextPlugins)
    {
        $this->container = $container;
        $apiApplicationContextPlugins = array_combine(
            array_map(function(ApiApplicationContextPluginInterface $apiApplicationContextPlugin) {return $apiApplicationContextPlugin->getKey();}, $apiApplicationContextPlugins),
            $apiApplicationContextPlugins
        );
        $key = $this->container->getApplicationService('apiApplicationContextKey');
        $this->apiApplicationContextPlugin = isset($apiApplicationContextPlugins[$key]) ? $apiApplicationContextPlugins[$key] : null;
    }

    /**
     * Validate http request plugins
     *
     * @return \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ValidateHttpRequestPluginInterface[]
     */
    public function getValidateHttpRequestPlugins(): array
    {
        return $this->apiApplicationContextPlugin ? $this->apiApplicationContextPlugin->getValidateHttpRequestPlugins() : $this->container->get(GlueApplicationDependencyProvider::PLUGIN_VALIDATE_HTTP_REQUEST);
    }

    /**
     * Plugins that called before processing {@link \Spryker\Glue\Kernel\Controller\FormattedAbstractController}.
     *
     * @return \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\FormattedControllerBeforeActionPluginInterface[]
     */
    public function getFormattedControllerBeforeActionTerminatePlugins(): array
    {
        return $this->apiApplicationContextPlugin ? $this->apiApplicationContextPlugin->getFormattedControllerBeforeActionTerminatePlugins() : $this->container->get(GlueApplicationDependencyProvider::PLUGIN_FORMATTED_CONTROLLER_BEFORE_ACTION);
    }

    /**
     * Format/Parse http request to internal rest resource request
     *
     * @return \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\FormatRequestPluginInterface[]
     */
    public function getFormatRequestPlugins(): array
    {
        return $this->apiApplicationContextPlugin ? $this->apiApplicationContextPlugin->getFormatRequestPlugins() : $this->container->get(GlueApplicationDependencyProvider::PLUGIN_FORMAT_REQUEST);
    }

    /**
     * Format response data the data which will send with http response
     *
     * @return \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\FormatResponseDataPluginInterface[]
     */
    public function getFormatResponseDataPlugins(): array
    {
        return $this->apiApplicationContextPlugin ? $this->apiApplicationContextPlugin->getFormatResponseDataPlugins() : $this->container->get(GlueApplicationDependencyProvider::PLUGIN_FORMAT_RESPONSE_DATA);
    }

    /**
     * Format/add additional response headers
     *
     * @return \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\FormatResponseHeadersPluginInterface[]
     */
    public function getFormatResponseHeadersPlugins(): array
    {
        return $this->apiApplicationContextPlugin ? $this->apiApplicationContextPlugin->getFormatResponseHeadersPlugins() : $this->container->get(GlueApplicationDependencyProvider::PLUGIN_FORMAT_RESPONSE_HEADERS);
    }

    /**
     * @return \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ValidateRestRequestPluginInterface[]
     */
    public function getValidateRestRequestPlugins(): array
    {
        return $this->apiApplicationContextPlugin ? $this->apiApplicationContextPlugin->getValidateRestRequestPlugins() : $this->container->get(GlueApplicationDependencyProvider::PLUGIN_VALIDATE_REST_REQUEST);
    }

    /**
     * @return \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\RestRequestValidatorPluginInterface[]
     */
    public function getRestRequestValidatorPlugins(): array
    {
        return $this->apiApplicationContextPlugin ? $this->apiApplicationContextPlugin->getRestRequestValidatorPlugins() : $this->container->get(GlueApplicationDependencyProvider::PLUGIN_REST_REQUEST_VALIDATOR);
    }

    /**
     * @return \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\RestUserValidatorPluginInterface[]
     */
    public function getRestUserValidatorPlugins(): array
    {
        return $this->apiApplicationContextPlugin ? $this->apiApplicationContextPlugin->getRestUserValidatorPlugins() : $this->container->get(GlueApplicationDependencyProvider::PLUGINS_VALIDATE_REST_USER);
    }

    /**
     * Called before invoking controller action
     *
     * @return \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ControllerBeforeActionPluginInterface[]
     */
    public function getControllerBeforeActionPlugins(): array
    {
        return $this->apiApplicationContextPlugin ? $this->apiApplicationContextPlugin->getControllerBeforeActionPlugins() : $this->container->get(GlueApplicationDependencyProvider::PLUGIN_CONTROLLER_BEFORE_ACTION);
    }

    /**
     * Called after done processing controller action
     *
     * @return \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ControllerAfterActionPluginInterface[]
     */
    public function getControllerAfterActionPlugins(): array
    {
        return $this->apiApplicationContextPlugin ? $this->apiApplicationContextPlugin->getControllerAfterActionPlugins() : $this->container->get(GlueApplicationDependencyProvider::PLUGIN_CONTROLLER_AFTER_ACTION);
    }

    /**
     * @return \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\RestUserFinderPluginInterface[]
     */
    public function getRestUserFinderPlugins(): array
    {
        return $this->apiApplicationContextPlugin ? $this->apiApplicationContextPlugin->getRestUserFinderPlugins() : $this->container->get(GlueApplicationDependencyProvider::PLUGINS_REST_USER_FINDER);
    }

    /**
     * @return \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\RouterParameterExpanderPluginInterface[]
     */
    public function getRouterParameterExpanderPlugins(): array
    {
        return $this->apiApplicationContextPlugin ? $this->apiApplicationContextPlugin->getRouterParameterExpanderPlugins() : $this->container->get(GlueApplicationDependencyProvider::PLUGINS_ROUTER_PARAMETER_EXPANDER);
    }

    /**
     * @return \Spryker\Shared\ApplicationExtension\Dependency\Plugin\ApplicationPluginInterface[]
     */
    public function getApplicationPlugins(): array
    {
        return $this->apiApplicationContextPlugin ? $this->apiApplicationContextPlugin->getApplicationPlugins() : $this->container->get(GlueApplicationDependencyProvider::PLUGINS_APPLICATION);
    }

    /**
     * Rest resource route plugin stack
     *
     * @return \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceRoutePluginInterface[]
     */
    public function getResourceRoutePlugins(): array
    {
        return $this->apiApplicationContextPlugin ? $this->apiApplicationContextPlugin->getResourceRoutePlugins() : $this->container->get(GlueApplicationDependencyProvider::PLUGIN_RESOURCE_ROUTES);
    }
}
