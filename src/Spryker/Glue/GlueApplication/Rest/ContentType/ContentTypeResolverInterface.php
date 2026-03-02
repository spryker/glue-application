<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Rest\ContentType;

use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Symfony\Component\HttpFoundation\Response;

interface ContentTypeResolverInterface
{
    public function matchContentType(string $contentType): array;

    public function addResponseHeaders(RestRequestInterface $restRequest, Response $httpResponse): void;
}
