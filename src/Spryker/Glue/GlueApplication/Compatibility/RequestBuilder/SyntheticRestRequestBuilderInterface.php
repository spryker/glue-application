<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\GlueApplication\Compatibility\RequestBuilder;

use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Symfony\Component\HttpFoundation\Request;

interface SyntheticRestRequestBuilderInterface
{
    public function build(
        Request $httpRequest,
        ?CustomerTransfer $customerTransfer,
        string $resourceShortName
    ): RestRequestInterface;
}
