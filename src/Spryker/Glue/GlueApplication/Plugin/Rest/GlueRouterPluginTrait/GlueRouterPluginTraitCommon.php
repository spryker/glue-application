<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Plugin\Rest\GlueRouterPluginTrait;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;

/**
 * @deprecated Will be removed without replacement. Exists only for BC reasons.
 */
trait GlueRouterPluginTraitCommon
{
    abstract protected function executeMatchRequest(Request $request): array;

    /**
     * @inheritDoc
     *
     * @return \Symfony\Component\Routing\RequestContext
     */
    abstract protected function executeGetContext(): RequestContext;

    abstract protected function executeGenerate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string;
}
