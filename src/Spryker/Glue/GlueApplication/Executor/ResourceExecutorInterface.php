<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Executor;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface;

interface ResourceExecutorInterface
{
    public function executeResource(
        ResourceInterface $resource,
        GlueRequestTransfer $glueRequestTransfer
    ): GlueResponseTransfer;
}
