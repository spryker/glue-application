<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Rest\Request\Data;

interface FilterInterface
{
    public function getResource(): string;

    public function getValue(): string;

    public function getField(): string;
}
