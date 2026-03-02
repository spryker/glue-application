<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Resource;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResourceMethodCollectionTransfer;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface;

class GenericResource implements ResourceInterface
{
    /**
     * @var callable
     */
    protected $executable;

    public function __construct(callable $executable)
    {
        $this->executable = $executable;
    }

    public function getResource(GlueRequestTransfer $glueRequestTransfer): callable
    {
        return $this->executable;
    }

    public function getController(): string
    {
        return '';
    }

    public function getType(): string
    {
        return '';
    }

    public function getDeclaredMethods(): GlueResourceMethodCollectionTransfer
    {
        return new GlueResourceMethodCollectionTransfer();
    }
}
