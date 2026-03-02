<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Rest\Request\Data;

class Filter implements FilterInterface
{
    /**
     * @var string
     */
    protected $resource;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var string
     */
    protected $field;

    public function __construct(string $resource, string $field, string $value)
    {
        $this->resource = $resource;
        $this->field = $field;
        $this->value = $value;
    }

    public function getResource(): string
    {
        return $this->resource;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getField(): string
    {
        return $this->field;
    }
}
