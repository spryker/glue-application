<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueApplication\Rest\ContentType;

use Codeception\Test\Unit;
use Spryker\Glue\GlueApplication\Rest\ContentType\ContentTypeResolver;
use Spryker\Glue\GlueApplication\Rest\ContentType\ContentTypeResolverInterface;
use SprykerTest\Glue\GlueApplication\Stub\RestRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueApplication
 * @group Rest
 * @group ContentType
 * @group ContentTypeResolverTest
 *
 * Add your own group annotations below this line
 */
class ContentTypeResolverTest extends Unit
{
    /**
     * @var string
     */
    protected $contentType = 'application/vnd.api+json; version=1.1';

    /**
     * @dataProvider contentTypeDataProvider
     *
     * @param string $contentType
     * @param string $expectedFormat
     * @param string|null $expectedVersion
     *
     * @return void
     */
    public function testMatchContentTypeShouldReturnContentTypeParts(
        string $contentType,
        string $expectedFormat,
        ?string $expectedVersion
    ): void {
        $contentTypeResolver = $this->createContentTypeResolver();

        $contentTypeParts = $contentTypeResolver->matchContentType($contentType);

        $this->assertSame($expectedFormat, $contentTypeParts[1]);

        if ($expectedVersion !== null) {
            $this->assertSame($expectedVersion, $contentTypeParts[2]);
        } else {
            $this->assertArrayNotHasKey(2, $contentTypeParts);
        }
    }

    /**
     * @return array<string, array<string, string|null>>
     */
    public function contentTypeDataProvider(): array
    {
        return [
            'basic content type with version' => [
                'contentType' => 'application/vnd.api+json; version=1.1',
                'expectedFormat' => 'json',
                'expectedVersion' => '1.1',
            ],
            'content type without version' => [
                'contentType' => 'application/vnd.api+json',
                'expectedFormat' => 'json',
                'expectedVersion' => null,
            ],
            'content type with charset' => [
                'contentType' => 'application/vnd.api+json; charset=utf-8',
                'expectedFormat' => 'json',
                'expectedVersion' => null,
            ],
            'content type with charset and version' => [
                'contentType' => 'application/vnd.api+json; charset=utf-8; version=2.0',
                'expectedFormat' => 'json',
                'expectedVersion' => '2.0',
            ],
            'content type with version and charset' => [
                'contentType' => 'application/vnd.api+json; version=1.5; charset=utf-8',
                'expectedFormat' => 'json',
                'expectedVersion' => '1.5',
            ],
            'content type with multiple parameters' => [
                'contentType' => 'application/vnd.api+json; charset=utf-8; version=3.14; boundary=something',
                'expectedFormat' => 'json',
                'expectedVersion' => '3.14',
            ],
        ];
    }

    /**
     * @return void
     */
    public function testAddResponseHeaderShouldAddJsonApiContentType(): void
    {
        $contentTypeResolver = $this->createContentTypeResolver();

        $restRequest = (new RestRequest())->createRestRequest();

        $httpResponse = new Response();
        $contentTypeResolver->addResponseHeaders($restRequest, $httpResponse);

        $contentType = $httpResponse->headers->get('Content-Type');

        $this->assertSame($this->contentType, $contentType);
    }

    /**
     * @return \Spryker\Glue\GlueApplication\Rest\ContentType\ContentTypeResolverInterface
     */
    protected function createContentTypeResolver(): ContentTypeResolverInterface
    {
        return new ContentTypeResolver();
    }
}
