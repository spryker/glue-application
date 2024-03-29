<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Cache\Writer;

interface ControllerCacheWriterInterface
{
    /**
     * @param string|null $apiApplication
     *
     * @return void
     */
    public function cache(?string $apiApplication = null): void;
}
