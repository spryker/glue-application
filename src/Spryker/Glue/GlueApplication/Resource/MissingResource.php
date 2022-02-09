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
    protected string $statusCode;

    /**
     * @var string
     */
    protected string $error;

    /**
     * @param string $statusCode
     * @param string $error
     */
    public function __construct(string $statusCode, string $error)
    {
        $this->statusCode = $statusCode;
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
                ->setCode($this->statusCode)
                ->setMessage($this->error);

            return (new GlueResponseTransfer())
                ->setStatus($this->statusCode)
                ->addError($glueErrorTransfer);
        };
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return '';
    }

    /**
     * @return \Generated\Shared\Transfer\GlueResourceMethodCollectionTransfer
     */
    public function getDeclaredMethods(): GlueResourceMethodCollectionTransfer
    {
        return new GlueResourceMethodCollectionTransfer();
    }
}
