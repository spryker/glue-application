<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueApplication\Serialize\Decoder;

use Codeception\Test\Unit;
use Spryker\Glue\GlueApplication\Dependency\Service\GlueApplicationToUtilEncodingServiceBridge;
use Spryker\Glue\GlueApplication\Serialize\Decoder\JsonDecoder;
use Spryker\Service\UtilEncoding\UtilEncodingServiceInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueApplication
 * @group Serialize
 * @group Decoder
 * @group JsonDecoderTest
 * Add your own group annotations below this line
 */
class JsonDecoderTest extends Unit
{
    public function testDecodeReturnsEmptyArrayWhenDataIsJsonScalar(): void
    {
        // Arrange: underlying service returns int (what json_decode("1234567890", true) produces)
        $utilEncodingServiceMock = $this->createMock(UtilEncodingServiceInterface::class);
        $utilEncodingServiceMock->method('decodeJson')->willReturn(1234567890);

        $decoder = new JsonDecoder(
            new GlueApplicationToUtilEncodingServiceBridge($utilEncodingServiceMock),
        );

        // Act
        $result = $decoder->decode('1234567890');

        // Assert
        $this->assertSame([], $result);
    }

    public function testDecodeReturnsArrayWhenDataIsValidJsonObject(): void
    {
        // Arrange
        $utilEncodingServiceMock = $this->createMock(UtilEncodingServiceInterface::class);
        $utilEncodingServiceMock->method('decodeJson')->willReturn(['key' => 'value']);

        $decoder = new JsonDecoder(
            new GlueApplicationToUtilEncodingServiceBridge($utilEncodingServiceMock),
        );

        // Act
        $result = $decoder->decode('{"key":"value"}');

        // Assert
        $this->assertSame(['key' => 'value'], $result);
    }
}
