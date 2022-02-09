<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Plugin\GlueApplication;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface;
use Spryker\Glue\GlueBackendApiApplicationExtension\Dependency\Plugin\RequestResourceFilterPluginInterface as BackendRequestResourceFilterPluginInterface;
use Spryker\Glue\GlueStorefrontApiApplicationExtension\Dependency\Plugin\RequestResourceFilterPluginInterface as StorefrontRequestResourceFilterPluginInterface;
use Spryker\Glue\Kernel\AbstractPlugin;

/**
 * @method \Spryker\Glue\GlueApplication\GlueApplicationFactory getFactory()
 */
class RequestResourceFilterPlugin extends AbstractPlugin implements StorefrontRequestResourceFilterPluginInterface, BackendRequestResourceFilterPluginInterface
{
    /**
     * Specification:
     * - Filters resources by GlueRequestTransfer resource name.
     * - Executes `ResourceFilterPluginInterface` stack of plugins and filters them by filtered resources.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     * @param array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface> $resourcePlugins
     *
     * @return \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface|null
     */
    public function filterResource(GlueRequestTransfer $glueRequestTransfer, array $resourcePlugins): ?ResourceInterface
    {
        return $this->getFactory()->createRequestResourcePluginFilter()->filterResourcePlugins($glueRequestTransfer, $resourcePlugins);
    }
}
