<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Rest\Version;

use Generated\Shared\Transfer\RestVersionTransfer;
use Symfony\Component\HttpFoundation\Request;

interface VersionResolverInterface
{
    public function findVersion(Request $request): RestVersionTransfer;

    public function getUrlVersionMatches(string $urlString): array;
}
