<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerTest\Glue\GlueApplication\Compatibility\Transfer;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\RestCartItemProductConfigurationInstanceAttributesTransfer;
use Generated\Shared\Transfer\RestCartItemsAttributesTransfer;
use Generated\Shared\Transfer\RestProductConfigurationPriceAttributesTransfer;
use Spryker\Glue\GlueApplication\Compatibility\Transfer\NullCollectionNormalizer;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueApplication
 * @group Compatibility
 * @group Transfer
 * @group NullCollectionNormalizerTest
 * Add your own group annotations below this line
 */
class NullCollectionNormalizerTest extends Unit
{
    protected const string SKU = '066_23294028';

    public function testGivenANullCollectionInANestedTransferWhenNormalizingThenTheCollectionBecomesAnEmptyList(): void
    {
        // Arrange
        $data = [
            RestCartItemsAttributesTransfer::SKU => static::SKU,
            RestCartItemsAttributesTransfer::PRODUCT_CONFIGURATION_INSTANCE => [
                RestCartItemProductConfigurationInstanceAttributesTransfer::PRICES => null,
            ],
        ];

        // Act
        $normalizedData = (new NullCollectionNormalizer())->normalize(RestCartItemsAttributesTransfer::class, $data);

        // Assert
        $restCartItemsAttributesTransfer = (new RestCartItemsAttributesTransfer())->fromArray($normalizedData, true);
        $productConfigurationInstance = $restCartItemsAttributesTransfer->getProductConfigurationInstanceOrFail();
        $this->assertCount(0, $productConfigurationInstance->getPrices());
        $this->assertTrue($productConfigurationInstance->isPropertyModified(RestCartItemProductConfigurationInstanceAttributesTransfer::PRICES));
    }

    public function testGivenANullScalarWhenNormalizingThenTheNullIsKeptAsSent(): void
    {
        // Arrange
        $data = [RestCartItemsAttributesTransfer::SKU => null];

        // Act
        $normalizedData = (new NullCollectionNormalizer())->normalize(RestCartItemsAttributesTransfer::class, $data);

        // Assert
        $this->assertSame($data, $normalizedData);
        $restCartItemsAttributesTransfer = (new RestCartItemsAttributesTransfer())->fromArray($normalizedData, true);
        $this->assertTrue($restCartItemsAttributesTransfer->isPropertyModified(RestCartItemsAttributesTransfer::SKU));
    }

    public function testGivenANullNestedTransferWhenNormalizingThenTheNullIsKeptAsSent(): void
    {
        // Arrange
        $data = [RestCartItemsAttributesTransfer::PRODUCT_CONFIGURATION_INSTANCE => null];

        // Act
        $normalizedData = (new NullCollectionNormalizer())->normalize(RestCartItemsAttributesTransfer::class, $data);

        // Assert
        $this->assertSame($data, $normalizedData);
    }

    public function testGivenANullCollectionInsideACollectionElementWhenNormalizingThenThatCollectionBecomesAnEmptyList(): void
    {
        // Arrange
        $data = [
            RestCartItemsAttributesTransfer::PRODUCT_CONFIGURATION_INSTANCE => [
                RestCartItemProductConfigurationInstanceAttributesTransfer::PRICES => [
                    [RestProductConfigurationPriceAttributesTransfer::VOLUME_PRICES => null],
                ],
            ],
        ];

        // Act
        $normalizedData = (new NullCollectionNormalizer())->normalize(RestCartItemsAttributesTransfer::class, $data);

        // Assert
        $restCartItemsAttributesTransfer = (new RestCartItemsAttributesTransfer())->fromArray($normalizedData, true);
        $priceAttributes = $restCartItemsAttributesTransfer->getProductConfigurationInstanceOrFail()->getPrices()->offsetGet(0);
        $this->assertCount(0, $priceAttributes->getVolumePrices());
    }

    public function testGivenAKeyTheTransferDoesNotKnowWhenNormalizingThenItIsLeftAsSent(): void
    {
        // Arrange
        $data = ['notAProperty' => null];

        // Act
        $normalizedData = (new NullCollectionNormalizer())->normalize(RestCartItemsAttributesTransfer::class, $data);

        // Assert
        $this->assertSame($data, $normalizedData);
    }
}
