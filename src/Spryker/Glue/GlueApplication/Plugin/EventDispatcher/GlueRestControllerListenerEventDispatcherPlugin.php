<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Plugin\EventDispatcher;

use Spryker\Glue\Kernel\AbstractPlugin;
use Spryker\Glue\Kernel\Controller\AbstractController;
use Spryker\Service\Container\ContainerInterface;
use Spryker\Shared\EventDispatcher\EventDispatcherInterface;
use Spryker\Shared\EventDispatcherExtension\Dependency\Plugin\EventDispatcherPluginInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @method \Spryker\Glue\GlueApplication\GlueApplicationFactory getFactory()
 */
class GlueRestControllerListenerEventDispatcherPlugin extends AbstractPlugin implements EventDispatcherPluginInterface
{
    public function extend(EventDispatcherInterface $eventDispatcher, ContainerInterface $container): EventDispatcherInterface
    {
        $eventDispatcher->addListener(KernelEvents::CONTROLLER, function (ControllerEvent $event) {
            $this->onKernelController($event);
        });

        return $eventDispatcher;
    }

    protected function onKernelController(ControllerEvent $event): void
    {
        $currentController = $event->getController();

        /**
         * When the SymfonyFrameworkRouterPlugin is used and the API Platform is enabled, we have to return early here.
         */
        if (!is_array($currentController)) {
            return;
        }

        [$controller, $action] = $currentController;

        $request = $event->getRequest();

        $apiController = function () use ($controller, $action, $request) {
            return $this->filter($controller, $action, $request);
        };

        $event->setController($apiController);
    }

    public function filter(AbstractController $controller, string $action, Request $request): Response
    {
        return $this->getFactory()->createRestControllerFilter()->filter($controller, $action, $request);
    }
}
