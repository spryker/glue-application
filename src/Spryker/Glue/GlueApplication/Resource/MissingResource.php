<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Resource;

use Generated\Shared\Transfer\GlueErrorTransfer;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResourceMethodCollectionTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\GlueApplication\Plugin\GlueApplication\AbstractResourcePlugin;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\MissingResourceInterface;
use Symfony\Component\HttpFoundation\Response;

class MissingResource extends AbstractResourcePlugin implements MissingResourceInterface
{
    /**
     * @var string
     */
    protected string $code;

    /**
     * @var string
     */
    protected string $error;

    public function __construct(string $code, string $error)
    {
        $this->code = $code;
        $this->error = $error;
    }

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return callable():\Generated\Shared\Transfer\GlueResponseTransfer
     */
    public function getResource(GlueRequestTransfer $glueRequestTransfer): callable
    {
        return function (): GlueResponseTransfer {
            $glueErrorTransfer = (new GlueErrorTransfer())
                ->setStatus(Response::HTTP_NOT_FOUND)
                ->setCode($this->code)
                ->setMessage($this->error);

            return (new GlueResponseTransfer())
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addError($glueErrorTransfer);
        };
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
