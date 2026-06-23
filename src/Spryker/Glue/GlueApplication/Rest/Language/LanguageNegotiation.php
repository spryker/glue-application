<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Rest\Language;

use Spryker\Glue\GlueApplication\Dependency\Client\GlueApplicationToStoreClientInterface;
use Spryker\Glue\GlueApplication\Dependency\Service\GlueApplicationToLocaleServiceInterface;

class LanguageNegotiation implements LanguageNegotiationInterface
{
    /**
     * @var \Spryker\Glue\GlueApplication\Dependency\Client\GlueApplicationToStoreClientInterface
     */
    protected GlueApplicationToStoreClientInterface $storeClient;

    /**
     * @var \Spryker\Glue\GlueApplication\Dependency\Service\GlueApplicationToLocaleServiceInterface
     */
    protected GlueApplicationToLocaleServiceInterface $localeService;

    public function __construct(
        GlueApplicationToStoreClientInterface $storeClient,
        GlueApplicationToLocaleServiceInterface $localeService
    ) {
        $this->storeClient = $storeClient;
        $this->localeService = $localeService;
    }

    public function getLanguageIsoCode(string $acceptLanguage): string
    {
        $storeTransfer = $this->storeClient->getCurrentStore();
        $storeLocaleCodes = $storeTransfer->getAvailableLocaleIsoCodes();
        /** @phpstan-var string $defaultLocaleIsoCode */
        $defaultLocaleIsoCode = $storeTransfer->getDefaultLocaleIsoCode() ?? current($storeLocaleCodes);

        if ($acceptLanguage === '') {
            return $defaultLocaleIsoCode;
        }

        // The Negotiation library matches BCP-47 language tags (e.g. `de-DE`). Store locale codes
        // may be keyed either by language code (`de`) or by full locale (`de_DE`, dynamic store),
        // both using underscores. Index the original keys by their normalized (hyphenated,
        // lower-cased) form so the negotiated result can be mapped back regardless of the keying.
        $storeKeyByNormalizedCode = [];
        foreach (array_keys($storeLocaleCodes) as $localeCode) {
            $storeKeyByNormalizedCode[strtolower(str_replace('_', '-', $localeCode))] = $localeCode;
        }

        $acceptLanguageTransfer = $this->localeService->getAcceptLanguage(
            $acceptLanguage,
            array_keys($storeKeyByNormalizedCode),
        );

        if (!$acceptLanguageTransfer || $acceptLanguageTransfer->getType() === null) {
            return $defaultLocaleIsoCode;
        }

        $normalizedType = strtolower($acceptLanguageTransfer->getType());

        if (!isset($storeKeyByNormalizedCode[$normalizedType])) {
            return $defaultLocaleIsoCode;
        }

        return $storeLocaleCodes[$storeKeyByNormalizedCode[$normalizedType]];
    }
}
