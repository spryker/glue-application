<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\ApiApplication;

use Generated\Shared\Transfer\GlueApiContextTransfer;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\GlueApplicationBootstrapPluginInterface;

interface ApiApplicationBootstrapResolverInterface
{
    public function resolveApiApplicationBootstrap(GlueApiContextTransfer $apiApplicationContext): GlueApplicationBootstrapPluginInterface;
}
