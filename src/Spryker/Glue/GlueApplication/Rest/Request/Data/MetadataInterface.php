<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Rest\Request\Data;

interface MetadataInterface
{
    public function getVersion(): ?VersionInterface;

    public function getAcceptFormat(): string;

    public function getContentTypeFormat(): string;

    public function getMethod(): string;

    public function getLocale(): string;

    public function isProtected(): bool;

    public function hasAttribute(string $key): bool;

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute(string $key);

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function setAttribute(string $key, $value): void;
}
