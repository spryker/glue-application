<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Rest\JsonApi;

use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

interface RestResourceBuilderInterface
{
    public function createRestResource(string $type, ?string $id = null, ?AbstractTransfer $attributeTransfer = null): RestResourceInterface;

    public function createRestResponse(int $totalItems = 0, int $limit = 0): RestResponseInterface;
}
