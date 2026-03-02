<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Rest;

use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

class ControllerCallbacks implements ControllerCallbacksInterface
{
    /**
     * @var array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ControllerBeforeActionPluginInterface>
     */
    protected $controllerBeforeActionPlugins = [];

    /**
     * @var array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ControllerAfterActionPluginInterface>
     */
    protected $controllerAfterActionPlugins = [];

    /**
     * @param array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ControllerBeforeActionPluginInterface> $controllerBeforeActionPlugins
     * @param array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ControllerAfterActionPluginInterface> $controllerAfterActionPlugins
     */
    public function __construct(array $controllerBeforeActionPlugins, array $controllerAfterActionPlugins)
    {
        $this->controllerBeforeActionPlugins = $controllerBeforeActionPlugins;
        $this->controllerAfterActionPlugins = $controllerAfterActionPlugins;
    }

    public function beforeAction(string $action, RestRequestInterface $restRequest): void
    {
        foreach ($this->controllerBeforeActionPlugins as $controllerBeforeActionPlugin) {
            $controllerBeforeActionPlugin->beforeAction($action, $restRequest);
        }
    }

    public function afterAction(
        string $action,
        RestRequestInterface $restRequest,
        RestResponseInterface $restResponse
    ): void {
        foreach ($this->controllerAfterActionPlugins as $controllerAfterActionPlugin) {
            $controllerAfterActionPlugin->afterAction($action, $restRequest, $restResponse);
        }
    }
}
