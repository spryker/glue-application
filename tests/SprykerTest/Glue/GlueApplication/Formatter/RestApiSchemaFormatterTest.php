<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueApplication\Formatter;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\AnnotationTransfer;
use Generated\Shared\Transfer\ApiApplicationSchemaContextTransfer;
use Generated\Shared\Transfer\PathAnnotationTransfer;
use Generated\Shared\Transfer\ResourceContextTransfer;
use Spryker\Glue\GlueApplication\Dependency\External\GlueApplicationToInflectorInterface;
use Spryker\Glue\GlueApplication\Formatter\Schema\RestApiSchemaFormatter;
use Spryker\Glue\GlueApplication\Formatter\Schema\RestApiSchemaParametersFormatterInterface;
use Spryker\Glue\GlueApplication\GlueApplicationConfig;
use stdClass;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueApplication
 * @group Formatter
 * @group RestApiSchemaFormatterTest
 * Add your own group annotations below this line
 */
class RestApiSchemaFormatterTest extends Unit
{
    /**
     * @var string
     */
    protected const PATH_COLLECTION = '/foo-bars';

    /**
     * @var string
     */
    protected const PATH_RESOURCE = '/foo-bars/{fooBarId}';

    public function testFormatKeepsDescriptionAndAddsNoBodySchemaForResponseWithoutContent(): void
    {
        // Arrange
        $formatter = $this->createFormatter();
        $formattedData = [
            'paths' => [
                static::PATH_RESOURCE => [
                    'get' => [
                        'responses' => [
                            '200' => ['description' => 'OK', 'content' => []],
                            '204' => ['description' => 'No Content.'],
                        ],
                    ],
                ],
            ],
        ];

        // Act
        $result = $formatter->format($formattedData, $this->createSchemaContextTransfer());

        // Assert
        $responses = $result['paths'][static::PATH_RESOURCE]['get']['responses'];
        $this->assertSame(['description' => 'No Content.'], $responses['204']);
        $this->assertArrayHasKey('content', $responses['200']);
    }

    protected function createFormatter(): RestApiSchemaFormatter
    {
        $inflectorMock = $this->createMock(GlueApplicationToInflectorInterface::class);
        $inflectorMock->method('singularize')->willReturnCallback(fn (string $value): string => rtrim($value, 's'));

        $parametersFormatterMock = $this->createMock(RestApiSchemaParametersFormatterInterface::class);
        $parametersFormatterMock->method('setOperationParameters')->willReturnArgument(0);
        $parametersFormatterMock->method('setComponentParameters')->willReturnArgument(0);

        return new RestApiSchemaFormatter($inflectorMock, $parametersFormatterMock, new GlueApplicationConfig());
    }

    protected function createSchemaContextTransfer(): ApiApplicationSchemaContextTransfer
    {
        $annotationTransfer = (new AnnotationTransfer())
            ->setResponseAttributesClassName('Generated\\Shared\\Transfer\\FooBarsRestAttributesTransfer');

        $resourceContextTransfer = (new ResourceContextTransfer())
            ->setResourceType('foo-bars')
            ->setResourcePluginName(stdClass::class)
            ->setPathAnnotation(
                (new PathAnnotationTransfer())
                    ->setGetResourceById($annotationTransfer)
                    ->setGetCollection($annotationTransfer),
            );

        return (new ApiApplicationSchemaContextTransfer())->addResourceContext($resourceContextTransfer);
    }
}
