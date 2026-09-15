<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueApplication\ApiApplication;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use ReflectionProperty;
use RuntimeException;
use Spryker\Glue\GlueApplication\ApiApplication\ApiApplicationProxy;
use Spryker\Glue\GlueApplication\ApiApplication\RequestFlowExecutorInterface;
use Spryker\Glue\GlueApplication\ApiApplication\Type\RequestFlowAgnosticApiApplication;
use Spryker\Glue\GlueApplication\ApiApplication\Type\RequestFlowAwareApiApplication;
use Spryker\Glue\GlueApplication\ContentNegotiator\ContentNegotiatorInterface;
use Spryker\Glue\GlueApplication\Exception\UnknownRequestFlowImplementationException;
use Spryker\Glue\GlueApplication\GlueApplicationConfig;
use Spryker\Glue\GlueApplication\Http\Request\RequestBuilderInterface;
use Spryker\Glue\GlueApplication\Http\Response\HttpSenderInterface;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\CommunicationProtocolPluginInterface;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ConventionPluginInterface;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\GlueApplicationBootstrapPluginInterface;
use Spryker\Service\Container\Container;
use Spryker\Service\Container\ContainerInterface;
use Spryker\Shared\Application\ApplicationInterface;
use Spryker\Shared\Application\Kernel;
use Spryker\Shared\Log\Config\LoggerConfigInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueApplication
 * @group ApiApplication
 * @group ApiApplicationProxyTest
 * Add your own group annotations below this line
 */
class ApiApplicationProxyTest extends Unit
{
    public function testBootIsExecutedOnBootBootstrapPlugin(): void
    {
        $apiApplicationConventionMock = $this->createMock(ConventionPluginInterface::class);
        $communicationProtocolPluginMock = $this->createMock(CommunicationProtocolPluginInterface::class);

        $requestFlowExecutorMock = $this->createMock(RequestFlowExecutorInterface::class);
        $applicationMock = $this->createMock(ApplicationInterface::class);
        $applicationMock
            ->expects($this->once())
            ->method('boot');

        $bootstrapPluginMock = $this->createMock(GlueApplicationBootstrapPluginInterface::class);
        $bootstrapPluginMock
            ->expects($this->once())
            ->method('getApplication')
            ->willReturn($applicationMock);

        $requestBuilderMock = $this->createMock(RequestBuilderInterface::class);
        $requestBuilderMock
            ->expects($this->never())
            ->method('extract');

        $httpSenderMock = $this->createMock(HttpSenderInterface::class);
        $httpSenderMock
            ->expects($this->never())
            ->method('sendResponse');

        $configMock = $this->createMock(GlueApplicationConfig::class);
        $configMock->method('isTerminationEnabled')
            ->willReturn(true);

        $apiApplicationProxy = new ApiApplicationProxy(
            $bootstrapPluginMock,
            $requestFlowExecutorMock,
            [$communicationProtocolPluginMock],
            [$apiApplicationConventionMock],
            $requestBuilderMock,
            $httpSenderMock,
            $this->createContentNegotiatorMock(),
            $this->createMock(Request::class),
            $configMock,
        );
        $apiApplicationProxy->boot();
    }

    public function testRunIsExecutedOnRequestFlowAgnosticBootstrapPlugin(): void
    {
        $apiApplicationConventionMock = $this->createMock(ConventionPluginInterface::class);
        $communicationProtocolPluginMock = $this->createMock(CommunicationProtocolPluginInterface::class);
        $requestFlowExecutorMock = $this->createMock(RequestFlowExecutorInterface::class);
        $requestFlowExecutorMock
            ->expects($this->never())
            ->method('executeRequestFlow');

        $applicationMock = $this->createMock(RequestFlowAgnosticApiApplication::class);
        $applicationMock
            ->expects($this->once())
            ->method('run');
        $applicationMock
            ->expects($this->never())
            ->method('terminate');

        $bootstrapPluginMock = $this->createMock(GlueApplicationBootstrapPluginInterface::class);
        $bootstrapPluginMock
            ->expects($this->once())
            ->method('getApplication')
            ->willReturn($applicationMock);

        $requestBuilderMock = $this->createMock(RequestBuilderInterface::class);
        $requestBuilderMock
            ->expects($this->never())
            ->method('extract');

        $httpSenderMock = $this->createMock(HttpSenderInterface::class);
        $httpSenderMock
            ->expects($this->never())
            ->method('sendResponse');

        $configMock = $this->createMock(GlueApplicationConfig::class);
        $configMock->method('isTerminationEnabled')
            ->willReturn(true);

        $apiApplicationProxy = new ApiApplicationProxy(
            $bootstrapPluginMock,
            $requestFlowExecutorMock,
            [$communicationProtocolPluginMock],
            [$apiApplicationConventionMock],
            $requestBuilderMock,
            $httpSenderMock,
            $this->createContentNegotiatorMock(),
            $this->createMock(Request::class),
            $configMock,
        );
        $apiApplicationProxy->run();
    }

    public function testExecuteRequestIsExecutedOnRequestFlowAwareApiApplicationPluginIfCommunicationProtocolIsDefined(): void
    {
        $apiApplicationConventionMock = $this->createMock(ConventionPluginInterface::class);
        $apiApplicationConventionMock
            ->expects($this->any())
            ->method('isApplicable')
            ->willReturn(true);

        $communicationProtocolPluginMock = $this->createMock(CommunicationProtocolPluginInterface::class);
        $communicationProtocolPluginMock
            ->expects($this->any())
            ->method('isApplicable')
            ->willReturn(true);

        $requestFlowExecutorMock = $this->createMock(RequestFlowExecutorInterface::class);
        $requestFlowExecutorMock
            ->expects($this->once())
            ->method('executeRequestFlow');

        $applicationMock = $this->createMock(RequestFlowAwareApiApplication::class);
        $applicationMock
            ->expects($this->never())
            ->method('run');

        $bootstrapPluginMock = $this->createMock(GlueApplicationBootstrapPluginInterface::class);
        $bootstrapPluginMock
            ->expects($this->once())
            ->method('getApplication')
            ->willReturn($applicationMock);

        $requestBuilderMock = $this->createMock(RequestBuilderInterface::class);
        $requestBuilderMock
            ->expects($this->never())
            ->method('extract');
        $applicationMock->expects($this->never())
            ->method('terminate');

        $httpSenderMock = $this->createMock(HttpSenderInterface::class);
        $httpSenderMock
            ->expects($this->never())
            ->method('sendResponse');

        $configMock = $this->createMock(GlueApplicationConfig::class);
        $configMock->method('isTerminationEnabled')
            ->willReturn(true);

        $apiApplicationProxy = new ApiApplicationProxy(
            $bootstrapPluginMock,
            $requestFlowExecutorMock,
            [$communicationProtocolPluginMock],
            [$apiApplicationConventionMock],
            $requestBuilderMock,
            $httpSenderMock,
            $this->createContentNegotiatorMock(),
            $this->createMock(Request::class),
            $configMock,
        );
        $apiApplicationProxy->run();
    }

    public function testExecuteRequestIsExecutedOnRequestFlowAwareApiApplicationPluginThoughDefaultHttpProtocolIfCommunicationPluginNotApplicable(): void
    {
        $apiApplicationConventionMock = $this->createMock(ConventionPluginInterface::class);

        $communicationProtocolPluginMock = $this->createMock(CommunicationProtocolPluginInterface::class);
        $communicationProtocolPluginMock
            ->expects($this->any())
            ->method('isApplicable')
            ->willReturn(false);

        $requestFlowExecutorMock = $this->createMock(RequestFlowExecutorInterface::class);
        $requestFlowExecutorMock
            ->expects($this->once())
            ->method('executeRequestFlow');

        $applicationMock = $this->createMock(RequestFlowAwareApiApplication::class);
        $applicationMock
            ->expects($this->never())
            ->method('run');

        $bootstrapPluginMock = $this->createMock(GlueApplicationBootstrapPluginInterface::class);
        $bootstrapPluginMock
            ->expects($this->once())
            ->method('getApplication')
            ->willReturn($applicationMock);

        $requestBuilderMock = $this->createMock(RequestBuilderInterface::class);
        $requestBuilderMock
            ->expects($this->once())
            ->method('extract');
        $applicationMock->expects($this->once())
            ->method('terminate');

        $httpSenderMock = $this->createMock(HttpSenderInterface::class);
        $httpSenderMock
            ->expects($this->once())
            ->method('sendResponse');

        $configMock = $this->createMock(GlueApplicationConfig::class);
        $configMock->method('isTerminationEnabled')
            ->willReturn(true);

        $apiApplicationProxy = new ApiApplicationProxy(
            $bootstrapPluginMock,
            $requestFlowExecutorMock,
            [$communicationProtocolPluginMock],
            [$apiApplicationConventionMock],
            $requestBuilderMock,
            $httpSenderMock,
            $this->createContentNegotiatorMock(),
            $this->createMock(Request::class),
            $configMock,
        );
        $apiApplicationProxy->run();
    }

    public function testExecuteRequestIsExecutedOnRequestFlowAwareApiApplicationPluginThoughDefaultHttpProtocol(): void
    {
        $apiApplicationConventionMock = $this->createMock(ConventionPluginInterface::class);
        $apiApplicationConventionMock
            ->expects($this->any())
            ->method('isApplicable')
            ->willReturn(true);

        $requestFlowExecutorMock = $this->createMock(RequestFlowExecutorInterface::class);
        $requestFlowExecutorMock
            ->expects($this->once())
            ->method('executeRequestFlow');

        $applicationMock = $this->createMock(RequestFlowAwareApiApplication::class);
        $applicationMock
            ->expects($this->never())
            ->method('run');
        $applicationMock->expects($this->once())
            ->method('terminate');

        $bootstrapPluginMock = $this->createMock(GlueApplicationBootstrapPluginInterface::class);
        $bootstrapPluginMock
            ->expects($this->once())
            ->method('getApplication')
            ->willReturn($applicationMock);

        $requestBuilderMock = $this->createMock(RequestBuilderInterface::class);
        $requestBuilderMock
            ->expects($this->once())
            ->method('extract');

        $httpSenderMock = $this->createMock(HttpSenderInterface::class);
        $httpSenderMock
            ->expects($this->once())
            ->method('sendResponse');

        $configMock = $this->createMock(GlueApplicationConfig::class);
        $configMock->method('isTerminationEnabled')
            ->willReturn(true);

        $apiApplicationProxy = new ApiApplicationProxy(
            $bootstrapPluginMock,
            $requestFlowExecutorMock,
            [],
            [$apiApplicationConventionMock],
            $requestBuilderMock,
            $httpSenderMock,
            $this->createContentNegotiatorMock(),
            $this->createMock(Request::class),
            $configMock,
        );
        $apiApplicationProxy->run();
    }

    public function testExceptionIsThrownIfNeitherRequestFlowAwareNorAgnosticIsImplemented(): void
    {
        $apiApplicationConventionMock = $this->createMock(ConventionPluginInterface::class);
        $bootstrapPluginMock = $this->createMock(GlueApplicationBootstrapPluginInterface::class);
        $requestFlowExecutorMock = $this->createMock(RequestFlowExecutorInterface::class);
        $requestBuilderMock = $this->createMock(RequestBuilderInterface::class);
        $httpSenderMock = $this->createMock(HttpSenderInterface::class);
        $configMock = $this->createMock(GlueApplicationConfig::class);
        $configMock->method('isTerminationEnabled')
            ->willReturn(true);

        $this->expectException(UnknownRequestFlowImplementationException::class);

        $apiApplicationProxy = new ApiApplicationProxy(
            $bootstrapPluginMock,
            $requestFlowExecutorMock,
            [],
            [$apiApplicationConventionMock],
            $requestBuilderMock,
            $httpSenderMock,
            $this->createContentNegotiatorMock(),
            $this->createMock(Request::class),
            $configMock,
        );
        $apiApplicationProxy->run();
    }

    public function testRunPreservesNotFoundStatusWhenApiPlatformKernelFallbackFails(): void
    {
        // Arrange
        $capturedGlueResponse = null;
        $apiApplicationProxy = $this->createApiApplicationProxyWithFailingKernel(new NotFoundHttpException(), $capturedGlueResponse);

        // Act
        $apiApplicationProxy->run();

        // Assert
        $this->assertNotNull($capturedGlueResponse);
        $this->assertSame(Response::HTTP_NOT_FOUND, $capturedGlueResponse->getHttpStatus());
    }

    public function testRunAnswersInternalServerErrorWhenApiPlatformKernelFailsWithoutHttpException(): void
    {
        // Arrange
        $capturedGlueResponse = null;
        $apiApplicationProxy = $this->createApiApplicationProxyWithFailingKernel(new RuntimeException('provider blew up'), $capturedGlueResponse);

        // Act
        $apiApplicationProxy->run();

        // Assert
        $this->assertNotNull($capturedGlueResponse);
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $capturedGlueResponse->getHttpStatus());
        $this->assertSame('application/vnd.api+json', $capturedGlueResponse->getFormat());
        $this->assertSame(
            ['errors' => [['status' => Response::HTTP_INTERNAL_SERVER_ERROR, 'detail' => 'Internal Server Error']]],
            json_decode((string)$capturedGlueResponse->getContent(), true),
        );
        $this->assertSame('provider blew up', $capturedGlueResponse->getErrors()[0]->getMessage());
    }

    public function testCreateKernelPropagatesTheDebugValueOfTheApplicationContainer(): void
    {
        // Arrange
        $container = new Container(['debug' => true]);
        $apiApplicationProxy = new class (
            $this->createMock(GlueApplicationBootstrapPluginInterface::class),
            $this->createMock(RequestFlowExecutorInterface::class),
            [],
            [],
            $this->createMock(RequestBuilderInterface::class),
            $this->createMock(HttpSenderInterface::class),
            $this->createContentNegotiatorMock(),
            $this->createMock(Request::class),
            $this->createMock(GlueApplicationConfig::class),
        ) extends ApiApplicationProxy {
            public function createKernelForContainer(ContainerInterface $container): Kernel
            {
                return $this->createKernel($container);
            }
        };

        // Act
        $kernel = $apiApplicationProxy->createKernelForContainer($container);

        // Assert
        $this->assertTrue((new ReflectionProperty(Kernel::class, 'debug'))->getValue($kernel));
    }

    protected function createApiApplicationProxyWithFailingKernel(Throwable $throwable, ?GlueResponseTransfer &$capturedGlueResponse): ApiApplicationProxy
    {
        $glueResponseTransfer = (new GlueResponseTransfer())
            ->setHttpStatus(Response::HTTP_NOT_FOUND)
            ->setHasExecutableResource(false);

        $requestFlowExecutorMock = $this->createMock(RequestFlowExecutorInterface::class);
        $requestFlowExecutorMock
            ->method('executeRequestFlow')
            ->willReturn($glueResponseTransfer);

        $applicationMock = $this->createMock(RequestFlowAwareApiApplication::class);
        $applicationMock->method('getContainer')->willReturn($this->createMock(ContainerInterface::class));

        $bootstrapPluginMock = $this->createMock(GlueApplicationBootstrapPluginInterface::class);
        $bootstrapPluginMock->method('getApplication')->willReturn($applicationMock);

        $httpSenderMock = $this->createMock(HttpSenderInterface::class);
        $httpSenderMock
            ->expects($this->once())
            ->method('sendResponse')
            ->willReturnCallback(function (GlueResponseTransfer $glueResponse) use (&$capturedGlueResponse): Response {
                $capturedGlueResponse = $glueResponse;

                return new Response();
            });

        $requestBuilderMock = $this->createMock(RequestBuilderInterface::class);
        $requestBuilderMock->method('extract')->willReturn(new GlueRequestTransfer());

        $configMock = $this->createMock(GlueApplicationConfig::class);
        $configMock->method('isTerminationEnabled')->willReturn(false);

        $kernelMock = $this->createMock(Kernel::class);
        $kernelMock->method('handle')->willThrowException($throwable);

        return new class (
            $kernelMock,
            $bootstrapPluginMock,
            $requestFlowExecutorMock,
            [],
            [],
            $requestBuilderMock,
            $httpSenderMock,
            $this->createContentNegotiatorMock(),
            $this->createMock(Request::class),
            $configMock,
        ) extends ApiApplicationProxy {
            public function __construct(protected Kernel $kernelMock, mixed ...$arguments)
            {
                parent::__construct(...$arguments);
            }

            protected function createKernel(ContainerInterface $container): Kernel
            {
                return $this->kernelMock;
            }

            protected function getLogger(?LoggerConfigInterface $loggerConfig = null): LoggerInterface
            {
                return new NullLogger();
            }
        };
    }

    protected function createContentNegotiatorMock(): ContentNegotiatorInterface
    {
        $contentNegotiatorMock = $this->createMock(ContentNegotiatorInterface::class);
        $contentNegotiatorMock
            ->expects($this->any())
            ->method('negotiate');

        return $contentNegotiatorMock;
    }
}
