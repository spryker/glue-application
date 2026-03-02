<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Router;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface;

interface RouteMatcherInterface
{
    public function route(GlueRequestTransfer $glueRequestTransfer): ResourceInterface;
}
