<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerTest\Glue\GlueApplication\Compatibility\EventSubscriber;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operations;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use Codeception\Test\Unit;
use Error;
use Generated\Shared\Transfer\CustomerTransfer;
use PHPUnit\Framework\MockObject\MockObject;
use Spryker\Glue\GlueApplication\Compatibility\EventSubscriber\GlueRestPluginStackSubscriber;
use Spryker\Glue\GlueApplication\Compatibility\RequestBuilder\SyntheticRestRequestBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ControllerBeforeActionPluginInterface;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\RestRequestValidatorPluginInterface;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueApplication
 * @group Compatibility
 * @group EventSubscriber
 * @group GlueRestPluginStackSubscriberTest
 * Add your own group annotations below this line
 */
class GlueRestPluginStackSubscriberTest extends Unit
{
    protected const string RESOURCE_SHORT_NAME = 'things';

    protected const string OPERATION_NAME = 'get_thing';

    protected const string PATH = '/things';

    /**
     * @uses \Spryker\Glue\GlueApplication\Compatibility\EventSubscriber\GlueRestPluginStackSubscriber::ATTRIBUTE_API_RESOURCE_CLASS
     */
    protected const string ATTRIBUTE_API_RESOURCE_CLASS = '_api_resource_class';

    /**
     * @uses \Spryker\Glue\GlueApplication\Compatibility\EventSubscriber\GlueRestPluginStackSubscriber::ATTRIBUTE_API_OPERATION_NAME
     */
    protected const string ATTRIBUTE_API_OPERATION_NAME = '_api_operation_name';

    /**
     * @uses \Spryker\Glue\GlueApplication\Compatibility\EventSubscriber\GlueRestPluginStackSubscriber::ATTRIBUTE_CUSTOMER_TRANSFER
     */
    protected const string ATTRIBUTE_CUSTOMER_TRANSFER = 'CustomerTransfer';

    public function testGivenARequestThatOptsOutOfTheGlueRestPluginStacksWhenItArrivesThenNoRequestValidatorPluginRuns(): void
    {
        // Arrange
        $restRequestValidatorPluginMock = $this->createRestRequestValidatorPluginMock();
        $request = $this->createApiPlatformRequest();
        $request->attributes->set(GlueRestPluginStackSubscriber::ATTRIBUTE_SKIP_GLUE_REST_PLUGIN_STACKS, true);

        // Assert
        $restRequestValidatorPluginMock->expects($this->never())->method('validate');

        // Act
        $this->createSubscriber($restRequestValidatorPluginMock)->onKernelRequest($this->createRequestEvent($request));
    }

    public function testGivenARequestThatDoesNotOptOutWhenItArrivesThenTheRequestValidatorPluginRuns(): void
    {
        // Arrange
        $restRequestValidatorPluginMock = $this->createRestRequestValidatorPluginMock();

        // Assert
        $restRequestValidatorPluginMock->expects($this->once())->method('validate')->willReturn(null);

        // Act
        $this->createSubscriber($restRequestValidatorPluginMock)->onKernelRequest($this->createRequestEvent($this->createApiPlatformRequest()));
    }

    public function testGivenABeforeActionPluginThrowingAnErrorWhenHandlingTheRequestThenTheErrorPropagates(): void
    {
        // Arrange
        $controllerBeforeActionPluginMock = $this->createMock(ControllerBeforeActionPluginInterface::class);
        $controllerBeforeActionPluginMock->method('beforeAction')->willThrowException(new Error('Plugin failed.'));
        $restRequestValidatorPluginMock = $this->createRestRequestValidatorPluginMock();
        $restRequestValidatorPluginMock->method('validate')->willReturn(null);
        $request = $this->createApiPlatformRequest();
        $request->attributes->set(static::ATTRIBUTE_CUSTOMER_TRANSFER, new CustomerTransfer());

        // Assert
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Plugin failed.');

        // Act
        $this->createSubscriber($restRequestValidatorPluginMock, [$controllerBeforeActionPluginMock])->onKernelRequest($this->createRequestEvent($request));
    }

    protected function createRestRequestValidatorPluginMock(): RestRequestValidatorPluginInterface&MockObject
    {
        return $this->createMock(RestRequestValidatorPluginInterface::class);
    }

    protected function createApiPlatformRequest(): Request
    {
        $request = Request::create(static::PATH);
        $request->attributes->set(static::ATTRIBUTE_API_RESOURCE_CLASS, stdClass::class);
        $request->attributes->set(static::ATTRIBUTE_API_OPERATION_NAME, static::OPERATION_NAME);

        return $request;
    }

    protected function createRequestEvent(Request $request): RequestEvent
    {
        return new RequestEvent($this->createMock(HttpKernelInterface::class), $request, HttpKernelInterface::MAIN_REQUEST);
    }

    /**
     * @param list<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ControllerBeforeActionPluginInterface> $controllerBeforeActionPlugins
     */
    protected function createSubscriber(
        RestRequestValidatorPluginInterface $restRequestValidatorPlugin,
        array $controllerBeforeActionPlugins = [],
    ): GlueRestPluginStackSubscriber {
        $operation = (new Get())->withShortName(static::RESOURCE_SHORT_NAME)->withName(static::OPERATION_NAME);
        $resourceMetadataCollectionFactoryMock = $this->createMock(ResourceMetadataCollectionFactoryInterface::class);
        $resourceMetadataCollectionFactoryMock->method('create')->willReturn(new ResourceMetadataCollection(stdClass::class, [
            (new ApiResource())->withShortName(static::RESOURCE_SHORT_NAME)->withOperations(new Operations([static::OPERATION_NAME => $operation])),
        ]));
        $syntheticRestRequestBuilderMock = $this->createMock(SyntheticRestRequestBuilderInterface::class);
        $syntheticRestRequestBuilderMock->method('build')->willReturn($this->createMock(RestRequestInterface::class));

        return new GlueRestPluginStackSubscriber(
            $resourceMetadataCollectionFactoryMock,
            $syntheticRestRequestBuilderMock,
            [],
            $controllerBeforeActionPlugins,
            [$restRequestValidatorPlugin],
        );
    }
}
