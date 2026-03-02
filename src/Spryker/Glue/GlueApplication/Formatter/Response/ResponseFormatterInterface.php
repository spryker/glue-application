<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Formatter\Response;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface;

interface ResponseFormatterInterface
{
    public function format(
        GlueResponseTransfer $glueResponseTransfer,
        GlueRequestTransfer $glueRequestTransfer,
        ?ResourceInterface $resource = null
    ): GlueResponseTransfer;
}
