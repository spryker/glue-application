<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\ContentNegotiator;

use Generated\Shared\Transfer\GlueRequestTransfer;

interface ContentNegotiatorInterface
{
    public function negotiate(GlueRequestTransfer $glueRequestTransfer): GlueRequestTransfer;
}
