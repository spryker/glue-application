<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Plugin\GlueApplication\Backend;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Spryker\Glue\GlueApplication\Exception\ControllerNotFoundException;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface;
use Spryker\Glue\Kernel\Backend\AbstractPlugin;
use Spryker\Glue\Kernel\Controller\AbstractController;

/**
 * @method \Spryker\Glue\GlueApplication\GlueApplicationFactory getFactory()
 */
abstract class BackendAbstractResourcePlugin extends AbstractPlugin implements ResourceInterface
{
    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return callable
     */
    public function getResource(GlueRequestTransfer $glueRequestTransfer): callable
    {
        $glueResourceMethodCollectionTransfer = $this->getDeclaredMethods();

        /** @var \Generated\Shared\Transfer\GlueResourceMethodConfigurationTransfer|null $glueResourceMethodConfigurationTransfer */
        $glueResourceMethodConfigurationTransfer = $glueResourceMethodCollectionTransfer
            ->offsetGet($glueRequestTransfer->getResource()->getMethod());

        if ($glueResourceMethodConfigurationTransfer) {
            $controller = $glueResourceMethodConfigurationTransfer->getController() ?? $this->getController();

            return [
                $this->getControllerObj($controller),
                $glueResourceMethodConfigurationTransfer->getAction() ?? $this->getActionName($glueRequestTransfer),
            ];
        }

        $controller = $this->getController();

        return [
            $this->getControllerObj($controller),
            $this->getActionName($glueRequestTransfer),
        ];
    }

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return string
     */
    private function getActionName(GlueRequestTransfer $glueRequestTransfer): string
    {
        $actionName = $glueRequestTransfer->getResource()->getMethod();
        if (!preg_match('/Action$/i', $actionName)) {
            $actionName = sprintf('%sAction', $actionName);
        }

        return $actionName;
    }

    /**
     * @param string $controller
     *
     * @throws \Spryker\Glue\GlueApplication\Exception\ControllerNotFoundException
     *
     * @return \Spryker\Glue\Kernel\Controller\AbstractController
     */
    private function getControllerObj(string $controller): AbstractController
    {
        if (class_exists($controller)) {
            $controller = new $controller();
        } else {
            throw new ControllerNotFoundException('Controller not found!');
        }

        return $controller;
    }
}
