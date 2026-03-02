<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Rest\Request\Data;

class Metadata implements MetadataInterface
{
    /**
     * @var \Spryker\Glue\GlueApplication\Rest\Request\Data\VersionInterface|null
     */
    protected $version;

    /**
     * @var string
     */
    protected $acceptFormat;

    /**
     * @var string
     */
    protected $contentTypeFormat;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var bool
     */
    protected $isProtected;

    /**
     * @var array
     */
    protected $attributes = [];

    public function __construct(
        string $acceptFormat,
        string $contentTypeFormat,
        string $method,
        string $locale,
        bool $isProtected,
        ?VersionInterface $version = null,
        array $attributes = []
    ) {
        $this->acceptFormat = $acceptFormat;
        $this->contentTypeFormat = $contentTypeFormat;
        $this->method = $method;
        $this->locale = $locale;
        $this->isProtected = $isProtected;
        $this->attributes = $attributes;
        $this->version = $version;
    }

    public function getVersion(): ?VersionInterface
    {
        return $this->version;
    }

    public function getAcceptFormat(): string
    {
        return $this->acceptFormat;
    }

    public function getContentTypeFormat(): string
    {
        return $this->contentTypeFormat;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function isProtected(): bool
    {
        return $this->isProtected;
    }

    public function hasAttribute(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute(string $key)
    {
        return $this->attributes[$key];
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }
}
