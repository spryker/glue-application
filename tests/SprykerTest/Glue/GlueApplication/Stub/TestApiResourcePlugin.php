<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueApplication\Stub;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResourceMethodCollectionTransfer;
use Generated\Shared\Transfer\GlueResourceMethodConfigurationTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface;

class TestApiResourcePlugin implements ResourceInterface
{
    protected string $resourceType;

    public function __construct(string $resourceType)
    {
        $this->resourceType = $resourceType;
    }

    public function getResource(GlueRequestTransfer $glueRequestTransfer): callable
    {
        return static fn (): GlueResponseTransfer => new GlueResponseTransfer();
    }

    public function getType(): string
    {
        return $this->resourceType;
    }

    public function getController(): string
    {
        return '';
    }

    public function getDeclaredMethods(): GlueResourceMethodCollectionTransfer
    {
        return (new GlueResourceMethodCollectionTransfer())
            ->setGetCollection(new GlueResourceMethodConfigurationTransfer())
            ->setGet(new GlueResourceMethodConfigurationTransfer());
    }
}
