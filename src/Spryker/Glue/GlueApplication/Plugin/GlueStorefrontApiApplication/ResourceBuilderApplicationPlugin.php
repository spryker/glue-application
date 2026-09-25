<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Plugin\GlueStorefrontApiApplication;

use Spryker\Glue\Kernel\AbstractPlugin;
use Spryker\Service\Container\ContainerInterface;
use Spryker\Shared\ApplicationExtension\Dependency\Plugin\ApplicationPluginInterface;

/**
 * Registers only the REST resource builder, unlike GlueApplicationApplicationPlugin, which also
 * overwrites the `debug` service off the Backend-oriented REST debug config.
 *
 * @method \Spryker\Glue\GlueApplication\GlueApplicationFactory getFactory()
 */
class ResourceBuilderApplicationPlugin extends AbstractPlugin implements ApplicationPluginInterface
{
    protected const string SERVICE_RESOURCE_BUILDER = 'resource_builder';

    public function provide(ContainerInterface $container): ContainerInterface
    {
        $container->set(static::SERVICE_RESOURCE_BUILDER, function () {
            return $this->getFactory()->createRestResourceBuilder();
        });

        return $container;
    }
}
