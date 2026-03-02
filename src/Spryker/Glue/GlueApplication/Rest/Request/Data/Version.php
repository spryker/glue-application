<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Rest\Request\Data;

class Version implements VersionInterface
{
    /**
     * @var int
     */
    protected $major;

    /**
     * @var int
     */
    protected $minor;

    public function __construct(int $major, int $minor)
    {
        $this->major = $major;
        $this->minor = $minor;
    }

    public function getMajor(): int
    {
        return $this->major;
    }

    public function getMinor(): int
    {
        return $this->minor;
    }
}
