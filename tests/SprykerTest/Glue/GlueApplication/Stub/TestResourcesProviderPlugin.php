<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueApplication\Stub;

use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourcesProviderPluginInterface;

class TestResourcesProviderPlugin implements ResourcesProviderPluginInterface
{
    /**
     * @var array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface>
     */
    protected array $resources;

    protected string $applicationName;

    /**
     * @param string $applicationName
     * @param array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface> $resources
     */
    public function __construct(string $applicationName, array $resources)
    {
        $this->applicationName = $applicationName;
        $this->resources = $resources;
    }

    public function getApplicationName(): string
    {
        return $this->applicationName;
    }

    /**
     * @return array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface>
     */
    public function getResources(): array
    {
        return $this->resources;
    }
}
