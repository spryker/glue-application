<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Rest;

use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

interface ControllerCallbacksInterface
{
    public function beforeAction(string $action, RestRequestInterface $restRequest): void;

    public function afterAction(string $action, RestRequestInterface $restRequest, RestResponseInterface $restResponse): void;
}
