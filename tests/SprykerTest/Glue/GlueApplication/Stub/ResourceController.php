<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueApplication\Stub;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\Kernel\Controller\AbstractController;

class ResourceController extends AbstractController
{
    public function getCollectionAction(GlueRequestTransfer $glueRequestTransfer): GlueResponseTransfer
    {
        return (new GlueResponseTransfer())
            ->setContent($glueRequestTransfer->getContent())
            ->addResource($glueRequestTransfer->getResource());
    }

    public function postAction(
        AttributesTransfer $attributesTransfer,
        GlueRequestTransfer $glueRequestTransfer
    ): GlueResponseTransfer {
        return (new GlueResponseTransfer())
            ->addResource($glueRequestTransfer->getResource())
            ->setContent($glueRequestTransfer->getContent());
    }

    public function getByIdAction(string $resourceId, GlueRequestTransfer $glueRequestTransfer): GlueResponseTransfer
    {
        return (new GlueResponseTransfer())
            ->addResource($glueRequestTransfer->getResource());
    }
}
