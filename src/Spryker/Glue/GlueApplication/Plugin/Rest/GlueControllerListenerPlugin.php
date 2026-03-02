<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Plugin\Rest;

use Spryker\Glue\Kernel\AbstractPlugin;
use Spryker\Glue\Kernel\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

/**
 * @deprecated Use {@link \Spryker\Glue\GlueApplication\Plugin\EventDispatcher\GlueRestControllerListenerEventDispatcherPlugin} instead.
 *
 * @method \Spryker\Glue\GlueApplication\GlueApplicationFactory getFactory()
 */
class GlueControllerListenerPlugin extends AbstractPlugin
{
    /**
     * @param \Symfony\Component\HttpKernel\Event\ControllerEvent $event
     *
     * @return callable|null
     */
    public function onKernelController(ControllerEvent $event)
    {
        $currentController = $event->getController();

        [$controller, $action] = $currentController;

        $request = $event->getRequest();
        $apiController = function () use ($controller, $action, $request) {
            return $this->filter($controller, $action, $request);
        };

        $event->setController($apiController);

        return null;
    }

    public function filter(AbstractController $controller, string $action, Request $request): Response
    {
        return $this->getFactory()->createRestControllerFilter()->filter($controller, $action, $request);
    }
}
