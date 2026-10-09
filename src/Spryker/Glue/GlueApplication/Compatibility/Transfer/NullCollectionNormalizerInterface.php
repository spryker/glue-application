<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\GlueApplication\Compatibility\Transfer;

interface NullCollectionNormalizerInterface
{
    /**
     * Specification:
     * - Replaces a `null` with an empty list wherever the transfer declares a collection property, at any depth.
     * - Keeps every other `null`, so a transfer hydrated from the result still tells a sent `null` from an absent key.
     *
     * @param class-string<\Spryker\Shared\Kernel\Transfer\AbstractTransfer> $transferClass
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    public function normalize(string $transferClass, array $data): array;
}
