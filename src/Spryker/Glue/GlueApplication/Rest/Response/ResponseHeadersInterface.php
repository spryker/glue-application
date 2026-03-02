<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Rest\Response;

use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Symfony\Component\HttpFoundation\Response;

interface ResponseHeadersInterface
{
    public function addHeaders(
        Response $httpResponse,
        RestResponseInterface $restResponse,
        RestRequestInterface $restRequest
    ): Response;

    public function addCorsAllowOriginHeader(Response $httpResponse): Response;
}
