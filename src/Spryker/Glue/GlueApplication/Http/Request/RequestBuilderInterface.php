<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Http\Request;

use Generated\Shared\Transfer\GlueRequestTransfer;

interface RequestBuilderInterface
{
    public function extract(GlueRequestTransfer $glueRequestTransfer): GlueRequestTransfer;
}
