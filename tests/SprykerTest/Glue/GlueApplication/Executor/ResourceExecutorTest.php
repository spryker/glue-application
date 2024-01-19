<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueApplication\Executor;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResourceMethodCollectionTransfer;
use Generated\Shared\Transfer\GlueResourceMethodConfigurationTransfer;
use Generated\Shared\Transfer\GlueResourceTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\GlueApplication\Cache\Reader\ControllerCacheReaderInterface;
use Spryker\Glue\GlueApplication\Cache\Writer\ControllerCacheWriterInterface;
use Spryker\Glue\GlueApplication\Exception\ControllerNotFoundException;
use Spryker\Glue\GlueApplication\Exception\InvalidActionParametersException;
use Spryker\Glue\GlueApplication\Executor\ResourceExecutor;
use Spryker\Glue\GlueApplication\GlueApplicationConfig;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface;
use SprykerTest\Glue\GlueApplication\Stub\AttributesTransfer;
use SprykerTest\Glue\GlueApplication\Stub\ResourceController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueApplication
 * @group Executor
 * @group ResourceExecutorTest
 * Add your own group annotations below this line
 */
class ResourceExecutorTest extends Unit
{
    /**
     * @return void
     */
    public function testGetResourceExecutesResourceWithControllerAndAction(): void
    {
        // Arrange
        $glueRequestTransfer = (new GlueRequestTransfer())
            ->setResource(new GlueResourceTransfer());

        $resourceMock = $this->createMock(ResourceInterface::class);
        $resourceMock->expects($this->once())
            ->method('getResource')
            ->willReturn([new ResourceController(), 'getCollectionAction']);

        $cacheReaderMock = $this->getMockBuilder(ControllerCacheReaderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cacheReaderMock->expects($this->once())
            ->method('getActionParameters')
            ->willReturn([
                GlueRequestTransfer::class => '',
                GlueResponseTransfer::class => '',
            ]);

        $cacheWriterMock = $this->getMockBuilder(ControllerCacheWriterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $config = new GlueApplicationConfig();

        // Act
        $glueResponseTransfer = (new ResourceExecutor($cacheReaderMock, $cacheWriterMock, $config))
            ->executeResource($resourceMock, $glueRequestTransfer);

        // Assert
        $this->assertInstanceOf(GlueResponseTransfer::class, $glueResponseTransfer);
        $this->assertCount(1, $glueResponseTransfer->getResources());
    }

    /**
     * @return void
     */
    public function testGetResourceExecutesResourceByResourceId(): void
    {
        // Arrange
        $glueRequestTransfer = (new GlueRequestTransfer())
            ->setResource((new GlueResourceTransfer())
                ->setId('FOO'));

        $resourceMock = $this->createMock(ResourceInterface::class);
        $resourceMock->expects($this->once())
            ->method('getResource')
            ->willReturn([new ResourceController(), 'getByIdAction']);

        $cacheReaderMock = $this->getMockBuilder(ControllerCacheReaderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cacheReaderMock->expects($this->once())
            ->method('getActionParameters')
            ->willReturn([
                'string' => '',
                GlueRequestTransfer::class => '',
                GlueResponseTransfer::class => '',
            ]);

        $cacheWriterMock = $this->getMockBuilder(ControllerCacheWriterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $config = new GlueApplicationConfig();

        // Act
        $glueResponseTransfer = (new ResourceExecutor($cacheReaderMock, $cacheWriterMock, $config))
            ->executeResource($resourceMock, $glueRequestTransfer);

        // Assert
        $resource = $glueResponseTransfer->getResources()->offsetGet(0);
        $this->assertSame('FOO', $resource->getId());
    }

    /**
     * @return void
     */
    public function testExecuteResourceExecutesIfContentExistsAndFirstArgumentDoesNotExist(): void
    {
        // Arrange
        $glueRequestTransfer = (new GlueRequestTransfer())
            ->setResource((new GlueResourceTransfer())->setMethod('get'))
            ->setContent('fooBar');

        $resourceMock = $this->createMock(ResourceInterface::class);
        $resourceMock->expects($this->once())
            ->method('getResource')
            ->willReturn([new ResourceController(), 'getCollectionAction']);
        $resourceMock->expects($this->once())
            ->method('getDeclaredMethods')
            ->willReturn(
                ((new GlueResourceMethodCollectionTransfer())
                    ->setGet((new GlueResourceMethodConfigurationTransfer())->setAction('getAction'))
                ),
            );

        $cacheReaderMock = $this->getMockBuilder(ControllerCacheReaderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cacheReaderMock->expects($this->once())
            ->method('getActionParameters')
            ->willReturn([
                GlueRequestTransfer::class => '',
                GlueResponseTransfer::class => '',
            ]);

        $cacheWriterMock = $this->getMockBuilder(ControllerCacheWriterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $config = new GlueApplicationConfig();

        // Act
        $glueResponseTransfer = (new ResourceExecutor($cacheReaderMock, $cacheWriterMock, $config))
            ->executeResource($resourceMock, $glueRequestTransfer);

        // Assert
        $this->assertInstanceOf(GlueResponseTransfer::class, $glueResponseTransfer);
        $this->assertSame($glueResponseTransfer->getContent(), $glueRequestTransfer->getContent());
    }

    /**
     * @return void
     */
    public function testExecuteResourceExecutesMethodInUpperCase(): void
    {
        // Arrange
        $glueRequestTransfer = (new GlueRequestTransfer())
            ->setResource((new GlueResourceTransfer())->setMethod('GET'))
            ->setContent('fooBar');

        $resourceMock = $this->createMock(ResourceInterface::class);
        $resourceMock->expects($this->once())
            ->method('getResource')
            ->willReturn([new ResourceController(), 'getCollectionAction']);

        $cacheReaderMock = $this->getMockBuilder(ControllerCacheReaderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cacheReaderMock->expects($this->once())
            ->method('getActionParameters')
            ->willReturn([
                GlueRequestTransfer::class => '',
                GlueResponseTransfer::class => '',
            ]);

        $cacheWriterMock = $this->getMockBuilder(ControllerCacheWriterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $config = new GlueApplicationConfig();

        // Act
        $glueResponseTransfer = (new ResourceExecutor($cacheReaderMock, $cacheWriterMock, $config))
            ->executeResource($resourceMock, $glueRequestTransfer);

        // Assert
        $this->assertInstanceOf(GlueResponseTransfer::class, $glueResponseTransfer);
        $this->assertSame($glueResponseTransfer->getContent(), $glueRequestTransfer->getContent());
    }

    /**
     * @return void
     */
    public function testExecuteResourceExecutesIfContentAndFirstArgumentExist(): void
    {
        // Arrange
        $glueRequestTransfer = (new GlueRequestTransfer())
            ->setResource((new GlueResourceTransfer())->setMethod(strtolower(Request::METHOD_POST)))
            ->setContent('fooBar');

        $resourceMock = $this->createMock(ResourceInterface::class);
        $resourceMock->expects($this->once())
            ->method('getResource')
            ->willReturn([new ResourceController(), 'postAction']);
        $resourceMock->expects($this->once())
            ->method('getDeclaredMethods')
            ->willReturn(
                ((new GlueResourceMethodCollectionTransfer())
                    ->setPost((new GlueResourceMethodConfigurationTransfer())->setAttributes(AttributesTransfer::class))
                ),
            );

        $cacheReaderMock = $this->getMockBuilder(ControllerCacheReaderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cacheReaderMock->expects($this->once())
            ->method('getActionParameters')
            ->willReturn([
                AttributesTransfer::class => '',
                GlueRequestTransfer::class => '',
                GlueResponseTransfer::class => '',
            ]);

        $cacheWriterMock = $this->getMockBuilder(ControllerCacheWriterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $config = new GlueApplicationConfig();

        // Act
        $glueResponseTransfer = (new ResourceExecutor($cacheReaderMock, $cacheWriterMock, $config))
            ->executeResource($resourceMock, $glueRequestTransfer);

        // Assert
        $this->assertInstanceOf(GlueResponseTransfer::class, $glueResponseTransfer);
        $this->assertSame($glueResponseTransfer->getContent(), $glueRequestTransfer->getContent());
    }

    /**
     * @return void
     */
    public function testExecuteResourceDoesNotExecuteIfControllerDoesNotExist(): void
    {
        // Arrange
        $glueRequestTransfer = (new GlueRequestTransfer())
            ->setResource(new GlueResourceTransfer());

        $resourceMock = $this->createMock(ResourceInterface::class);
        $resourceMock->expects($this->once())
            ->method('getResource')
            ->willThrowException($this->createMock(ControllerNotFoundException::class));

        $cacheReaderMock = $this->getMockBuilder(ControllerCacheReaderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cacheReaderMock
            ->method('getActionParameters')
            ->willReturn(null);

        $cacheWriterMock = $this->getMockBuilder(ControllerCacheWriterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configMock = $this->getMockBuilder(GlueApplicationConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Assert
        $this->expectException(ControllerNotFoundException::class);

        // Act
        (new ResourceExecutor($cacheReaderMock, $cacheWriterMock, $configMock))
            ->executeResource($resourceMock, $glueRequestTransfer);
    }

    /**
     * @return void
     */
    public function testExecuteResourceThrowsExceptionIfCachedDataDoesNotExist(): void
    {
        // Arrange
        $glueRequestTransfer = (new GlueRequestTransfer())
            ->setResource(new GlueResourceTransfer());

        $resourceMock = $this->createMock(ResourceInterface::class);
        $resourceMock->expects($this->once())
            ->method('getResource')
            ->willReturn([new ResourceController(), 'getCollectionAction']);

        $cacheReaderMock = $this->getMockBuilder(ControllerCacheReaderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cacheReaderMock->expects($this->once())
            ->method('getActionParameters')
            ->willReturn(null);

        $cacheWriterMock = $this->getMockBuilder(ControllerCacheWriterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configMock = $this->getMockBuilder(GlueApplicationConfig::class)
            ->disableOriginalConstructor()
            ->getMock();
        $configMock->expects($this->once())
            ->method('isDevelopmentMode')
            ->willReturn(false);

        // Assert
        $this->expectException(InvalidActionParametersException::class);

        // Act
        (new ResourceExecutor($cacheReaderMock, $cacheWriterMock, $configMock))
            ->executeResource($resourceMock, $glueRequestTransfer);
    }

    /**
     * @return void
     */
    public function testExecuteResourceWarmsUpCacheForTheConfiguredRoutes(): void
    {
        // Arrange
        $routePatterns = [
            '/^\/dynamic-resource\/.+/',
        ];

        $glueRequestTransfer = (new GlueRequestTransfer())
            ->setResource((new GlueResourceTransfer())->setMethod('get'))
            ->setContent('fooBar')
            ->setPath('/dynamic-resource/some-resource');

        $resourceMock = $this->createMock(ResourceInterface::class);
        $resourceMock->expects($this->once())
            ->method('getResource')
            ->willReturn([new ResourceController(), 'getCollectionAction']);

        $configMock = $this->getMockBuilder(GlueApplicationConfig::class)
            ->disableOriginalConstructor()
            ->getMock();
        $configMock->method('isDevelopmentMode')->willReturn(false);
        $configMock->method('getRoutePatternsAllowedForCacheWarmUp')->willReturn($routePatterns);

        $cacheReaderMock = $this->getMockBuilder(ControllerCacheReaderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cacheReaderMock->method('getActionParameters')
            ->willReturnOnConsecutiveCalls(
                null,
                [
                    GlueRequestTransfer::class => '',
                    GlueResponseTransfer::class => '',
                ],
            );

        $cacheWriterMock = $this->getMockBuilder(ControllerCacheWriterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cacheWriterMock->expects($this->once())
            ->method('cache');

        $resourceExecutor = new ResourceExecutor($cacheReaderMock, $cacheWriterMock, $configMock);

        // Act
        $glueResponseTransfer = $resourceExecutor->executeResource($resourceMock, $glueRequestTransfer);

        // Assert
        $this->assertSame($glueResponseTransfer->getContent(), $glueRequestTransfer->getContent());
        $this->assertCount(1, $glueResponseTransfer->getResources());
    }
}
