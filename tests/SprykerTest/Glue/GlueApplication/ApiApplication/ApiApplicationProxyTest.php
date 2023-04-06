<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueApplication\ApiApplication;

use Codeception\Test\Unit;
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
use Spryker\Shared\Application\ApplicationInterface;
use Symfony\Component\HttpFoundation\Request;

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
    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return \Spryker\Glue\GlueApplication\ContentNegotiator\ContentNegotiatorInterface
     */
    protected function createContentNegotiatorMock(): ContentNegotiatorInterface
    {
        $contentNegotiatorMock = $this->createMock(ContentNegotiatorInterface::class);
        $contentNegotiatorMock
            ->expects($this->any())
            ->method('negotiate');

        return $contentNegotiatorMock;
    }
}
