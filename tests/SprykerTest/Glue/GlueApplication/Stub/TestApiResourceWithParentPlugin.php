<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueApplication\Stub;

use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceWithParentPluginInterface;

class TestApiResourceWithParentPlugin extends TestApiResourcePlugin implements ResourceWithParentPluginInterface
{
    protected string $parentResourceType;

    public function __construct(string $resourceType, string $parentResourceType)
    {
        parent::__construct($resourceType);

        $this->parentResourceType = $parentResourceType;
    }

    public function getParentResourceType(): string
    {
        return $this->parentResourceType;
    }
}
