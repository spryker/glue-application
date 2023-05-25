<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Builder\Request;

use Generated\Shared\Transfer\GlueFilterTransfer;
use Generated\Shared\Transfer\GlueRequestTransfer;

class FilterFieldRequestBuilder implements RequestBuilderInterface
{
    /**
     * @var string
     */
    protected const QUERY_FILTER = 'filter';

    /**
     * Specification:
     * - Extracts `GlueRequestTransfer.filter` from the `GlueRequestTransfer.queryFields`.
     * - Splits query filter keys by dot and interprets them as `GlueFilterTransfer`.
     *
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return \Generated\Shared\Transfer\GlueRequestTransfer
     */
    public function build(GlueRequestTransfer $glueRequestTransfer): GlueRequestTransfer
    {
        $queryParameters = $glueRequestTransfer->getQueryFields();

        if (!isset($queryParameters[static::QUERY_FILTER]) || !is_array($queryParameters[static::QUERY_FILTER])) {
            return $glueRequestTransfer;
        }

        foreach ($queryParameters[static::QUERY_FILTER] as $key => $value) {
            $explodedKey = explode('.', $key);
            $glueRequestTransfer->addFilter(
                (new GlueFilterTransfer())
                    ->setResource($explodedKey[0])
                    ->setField($explodedKey[1] ?? null)
                    ->setValue($value),
            );
        }

        return $glueRequestTransfer;
    }
}
