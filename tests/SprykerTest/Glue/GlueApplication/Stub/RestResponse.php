<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueApplication\Stub;

use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilder;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;

class RestResponse
{
    public function createRestResponse(): RestResponseInterface
    {
        $restResourceBuilder = new RestResourceBuilder();

        $restResponse = $restResourceBuilder->createRestResponse(20);

        $restResource = $restResourceBuilder->createRestResource('tests', 1);
        $restResponse->addResource($restResource);

        return $restResponse;
    }
}
