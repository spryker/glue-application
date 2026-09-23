<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerTest\Glue\GlueApplication\Compatibility\RequestBuilder;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\RestAccessTokensAttributesTransfer;
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
}
