<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Plugin\Rest;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Spryker\Glue\Kernel\AbstractPlugin;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @deprecated Use {@link \Spryker\Glue\GlueApplication\Plugin\EventDispatcher\GlueRestControllerListenerEventDispatcherPlugin} instead.
 *
 * @method \Spryker\Glue\GlueApplication\GlueApplicationFactory getFactory()
 */
class GlueServiceProviderPlugin extends AbstractPlugin implements ServiceProviderInterface
{
    public function register(Application $app): void
    {
    }

    public function boot(Application $app): void
    {
        $eventDispatcher = $this->getEventDispatcher($app);

        $eventDispatcher->addListener(
            KernelEvents::CONTROLLER,
            [
                $this->getFactory()->createRestControllerListener(),
                'onKernelController',
            ],
        );
    }

    protected function getEventDispatcher(Application $app): EventDispatcherInterface
    {
        return $app['dispatcher'];
    }
}
