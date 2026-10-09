<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerTest\Glue\GlueApplication\Compatibility\RequestBuilder;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\RestAccessTokensAttributesTransfer;
use Generated\Shared\Transfer\RestCartItemProductConfigurationInstanceAttributesTransfer;
use Generated\Shared\Transfer\RestCartItemsAttributesTransfer;
use Spryker\Glue\GlueApplication\Compatibility\RequestBuilder\SyntheticRestRequestBuilder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueApplication
 * @group Compatibility
 * @group RequestBuilder
 * @group SyntheticRestRequestBuilderTest
 * Add your own group annotations below this line
 */
class SyntheticRestRequestBuilderTest extends Unit
{
    protected const string RESOURCE_SHORT_NAME = 'access-tokens';

    protected const string PATH = '/access-tokens';

    protected const string USERNAME = 'spencor.hopkin@acme.com';

    protected const string BODY_WITH_STRING_ATTRIBUTES = '{"data":{"type":"access-tokens","attributes":"x"}}';

    protected const string BODY_WITH_OBJECT_ATTRIBUTES = '{"data":{"type":"access-tokens","attributes":{"username":"spencor.hopkin@acme.com"}}}';

    protected const string RESOURCE_SHORT_NAME_CART_ITEMS = 'items';

    protected const string PATH_CART_ITEMS = '/carts/8d4e0c5e-5f2a-4d3b-9e8f-1a2b3c4d5e6f/items';

    protected const string BODY_WITH_NULL_SKU_AND_NULL_PRICES = '{"data":{"type":"items","attributes":{"sku":null,"productConfigurationInstance":{"prices":null}}}}';

    /**
     * A JSON:API document whose `attributes` member is not an object is malformed; the bridge must build an empty
     * attributes transfer for the request validator to reject the document instead of failing on `fromArray()`.
     */
    public function testGivenBodyWhoseAttributesAreNotAnObjectWhenBuildingThenTheAttributesTransferStaysEmpty(): void
    {
        // Arrange
        $request = Request::create(static::PATH, Request::METHOD_POST, content: static::BODY_WITH_STRING_ATTRIBUTES);

        // Act
        $restRequest = (new SyntheticRestRequestBuilder())->build($request, null, static::RESOURCE_SHORT_NAME, RestAccessTokensAttributesTransfer::class);

        // Assert
        $attributesTransfer = $restRequest->getResource()->getAttributes();
        $this->assertInstanceOf(RestAccessTokensAttributesTransfer::class, $attributesTransfer);
        $this->assertSame([], $attributesTransfer->modifiedToArray());
    }

    public function testGivenBodyWhoseAttributesAreAnObjectWhenBuildingThenTheAttributesTransferIsPopulated(): void
    {
        // Arrange
        $request = Request::create(static::PATH, Request::METHOD_POST, content: static::BODY_WITH_OBJECT_ATTRIBUTES);

        // Act
        $restRequest = (new SyntheticRestRequestBuilder())->build($request, null, static::RESOURCE_SHORT_NAME, RestAccessTokensAttributesTransfer::class);

        // Assert
        $attributesTransfer = $restRequest->getResource()->getAttributes();
        $this->assertInstanceOf(RestAccessTokensAttributesTransfer::class, $attributesTransfer);
        $this->assertSame(static::USERNAME, $attributesTransfer->getUsername());
    }

    public function testGivenBodyWithANullCollectionAndANullScalarWhenBuildingThenOnlyTheCollectionIsNormalized(): void
    {
        // Arrange
        $request = Request::create(static::PATH_CART_ITEMS, Request::METHOD_POST, content: static::BODY_WITH_NULL_SKU_AND_NULL_PRICES);

        // Act
        $restRequest = (new SyntheticRestRequestBuilder())->build($request, null, static::RESOURCE_SHORT_NAME_CART_ITEMS, RestCartItemsAttributesTransfer::class);

        // Assert
        /** @var \Generated\Shared\Transfer\RestCartItemsAttributesTransfer $attributesTransfer */
        $attributesTransfer = $restRequest->getResource()->getAttributes();
        $this->assertTrue($attributesTransfer->isPropertyModified(RestCartItemsAttributesTransfer::SKU));
        $this->assertNull($attributesTransfer->getSku());
        $productConfigurationInstance = $attributesTransfer->getProductConfigurationInstanceOrFail();
        $this->assertTrue($productConfigurationInstance->isPropertyModified(RestCartItemProductConfigurationInstanceAttributesTransfer::PRICES));
        $this->assertCount(0, $productConfigurationInstance->getPrices());
    }
}
