<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueApplication\Router\ResourceRouter;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Spryker\Glue\GlueApplication\Resource\MissingResource;
use Spryker\Glue\GlueApplication\Router\ResourceRouter\ConventionResourceFilter;
use Spryker\Glue\GlueApplication\Router\ResourceRouter\RequestResourcePluginFilter;
use Spryker\Glue\GlueApplication\Router\ResourceRouter\ResourceRouteMatcher;
use Spryker\Glue\GlueApplication\Router\ResourceRouter\Uri\UriParser;
use SprykerTest\Glue\GlueApplication\GlueApplicationTester;
use SprykerTest\Glue\GlueApplication\Stub\TestApiResourcePlugin;
use SprykerTest\Glue\GlueApplication\Stub\TestApiResourceWithParentPlugin;
use SprykerTest\Glue\GlueApplication\Stub\TestResourcesProviderPlugin;
use Symfony\Component\HttpFoundation\Request;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueApplication
 * @group Router
 * @group ResourceRouter
 * @group ResourceRouteMatcherTest
 * Add your own group annotations below this line
 */
class ResourceRouteMatcherTest extends Unit
{
    /**
     * @var string
     */
    protected const APPLICATION_NAME = 'GLUE_STOREFRONT';

    /**
     * @var string
     */
    protected const APPLICATION_NAME_BACKEND = 'GLUE_BACKEND';

    /**
     * @var string
     */
    protected const RESOURCE_TYPE_PARENT = 'parent-resources';

    /**
     * @var string
     */
    protected const RESOURCE_TYPE_CHILD_WITH_PARENT = 'child-resources';

    /**
     * @var string
     */
    protected const RESOURCE_TYPE_SERVICE_POINTS = 'service-points';

    /**
     * @var string
     */
    protected const RESOURCE_TYPE_PICKING_LISTS = 'picking-lists';

    /**
     * @var string
     */
    protected const RESOURCE_ID = '262feb9d-33a7-5c55-9b04-45b1fd22067e';

    /**
     * @var \SprykerTest\Glue\GlueApplication\GlueApplicationTester
     */
    protected GlueApplicationTester $tester;

    /**
     * Accessing a resource that implements ResourceWithParentPluginInterface directly (without its required parent
     * in the URL) must return a MissingResource instead of crashing.
     *
     * URL under test: /child-resources
     * Valid URL: /parent-resources/{id}/child-resources
     */
    public function testRouteReturnsMissingResourceWhenChildResourceIsAccessedWithoutItsParent(): void
    {
        //Arrange
        $resourceRouteMatcher = $this->createResourceRouteMatcher([
            new TestApiResourceWithParentPlugin(static::RESOURCE_TYPE_CHILD_WITH_PARENT, static::RESOURCE_TYPE_PARENT),
        ]);

        $glueRequestTransfer = (new GlueRequestTransfer())
            ->setApplication(static::APPLICATION_NAME)
            ->setPath(sprintf('/%s', static::RESOURCE_TYPE_CHILD_WITH_PARENT))
            ->setMethod(Request::METHOD_GET);

        //Act
        $resource = $resourceRouteMatcher->route($glueRequestTransfer);

        //Assert
        $this->assertInstanceOf(MissingResource::class, $resource);
    }

    /**
     * A resource that has no declared parent (does not implement ResourceWithParentPluginInterface) must NOT be
     * reachable through an arbitrary parent prefix in the URL.
     *
     * URL under test: /picking-lists/{id}/service-points
     * Valid URL: /service-points
     */
    public function testRouteReturnsMissingResourceWhenResourceWithoutParentIsAccessedViaArbitraryParent(): void
    {
        //Arrange
        $resourceRouteMatcher = $this->createResourceRouteMatcher([
            new TestApiResourcePlugin(static::RESOURCE_TYPE_PICKING_LISTS),
            new TestApiResourcePlugin(static::RESOURCE_TYPE_SERVICE_POINTS),
        ]);

        $glueRequestTransfer = (new GlueRequestTransfer())
            ->setApplication(static::APPLICATION_NAME)
            ->setPath(sprintf('/%s/%s/%s', static::RESOURCE_TYPE_PICKING_LISTS, static::RESOURCE_ID, static::RESOURCE_TYPE_SERVICE_POINTS))
            ->setMethod(Request::METHOD_GET);

        //Act
        $resource = $resourceRouteMatcher->route($glueRequestTransfer);

        //Assert
        $this->assertInstanceOf(MissingResource::class, $resource);
    }

    /**
     * Accessing a resource that implements ResourceWithParentPluginInterface directly (without its required parent
     * in the URL) must return a MissingResource instead of crashing — backend API context.
     *
     * URL under test: /child-resources
     * Valid URL: /parent-resources/{id}/child-resources
     */
    public function testRouteReturnsMissingResourceWhenChildResourceIsAccessedWithoutItsParentForBackendApi(): void
    {
        //Arrange
        $resourceRouteMatcher = $this->createResourceRouteMatcherForApplication(
            static::APPLICATION_NAME_BACKEND,
            [new TestApiResourceWithParentPlugin(static::RESOURCE_TYPE_CHILD_WITH_PARENT, static::RESOURCE_TYPE_PARENT)],
        );

        $glueRequestTransfer = (new GlueRequestTransfer())
            ->setApplication(static::APPLICATION_NAME_BACKEND)
            ->setPath(sprintf('/%s', static::RESOURCE_TYPE_CHILD_WITH_PARENT))
            ->setMethod(Request::METHOD_GET);

        //Act
        $resource = $resourceRouteMatcher->route($glueRequestTransfer);

        //Assert
        $this->assertInstanceOf(MissingResource::class, $resource);
    }

    /**
     * A resource that has no declared parent (does not implement ResourceWithParentPluginInterface) must NOT be
     * reachable through an arbitrary parent prefix in the URL — backend API context.
     *
     * URL under test: /picking-lists/{id}/service-points
     * Valid URL: /service-points
     */
    public function testRouteReturnsMissingResourceWhenResourceWithoutParentIsAccessedViaArbitraryParentForBackendApi(): void
    {
        //Arrange
        $resourceRouteMatcher = $this->createResourceRouteMatcherForApplication(
            static::APPLICATION_NAME_BACKEND,
            [
                new TestApiResourcePlugin(static::RESOURCE_TYPE_PICKING_LISTS),
                new TestApiResourcePlugin(static::RESOURCE_TYPE_SERVICE_POINTS),
            ],
        );

        $glueRequestTransfer = (new GlueRequestTransfer())
            ->setApplication(static::APPLICATION_NAME_BACKEND)
            ->setPath(sprintf('/%s/%s/%s', static::RESOURCE_TYPE_PICKING_LISTS, static::RESOURCE_ID, static::RESOURCE_TYPE_SERVICE_POINTS))
            ->setMethod(Request::METHOD_GET);

        //Act
        $resource = $resourceRouteMatcher->route($glueRequestTransfer);

        //Assert
        $this->assertInstanceOf(MissingResource::class, $resource);
    }

    /**
     * A resource that declares a parent (implements ResourceWithParentPluginInterface) must remain
     * reachable through its declared parent in the URL.
     *
     * URL under test: /parent-resources/{id}/child-resources
     * Valid URL: /parent-resources/{id}/child-resources
     */
    public function testRouteReturnsResourceWhenChildResourceIsAccessedWithItsParent(): void
    {
        //Arrange
        $resourceRouteMatcher = $this->createResourceRouteMatcher([
            new TestApiResourcePlugin(static::RESOURCE_TYPE_PARENT),
            new TestApiResourceWithParentPlugin(static::RESOURCE_TYPE_CHILD_WITH_PARENT, static::RESOURCE_TYPE_PARENT),
        ]);

        $glueRequestTransfer = (new GlueRequestTransfer())
            ->setApplication(static::APPLICATION_NAME)
            ->setPath(sprintf('/%s/%s/%s', static::RESOURCE_TYPE_PARENT, static::RESOURCE_ID, static::RESOURCE_TYPE_CHILD_WITH_PARENT))
            ->setMethod(Request::METHOD_GET);

        //Act
        $resource = $resourceRouteMatcher->route($glueRequestTransfer);

        //Assert
        $this->assertNotInstanceOf(MissingResource::class, $resource);
    }

    /**
     * @param array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface> $resourcePlugins
     */
    protected function createResourceRouteMatcher(array $resourcePlugins): ResourceRouteMatcher
    {
        return $this->createResourceRouteMatcherForApplication(static::APPLICATION_NAME, $resourcePlugins);
    }

    /**
     * @param array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface> $resourcePlugins
     */
    protected function createResourceRouteMatcherForApplication(string $applicationName, array $resourcePlugins): ResourceRouteMatcher
    {
        return new ResourceRouteMatcher(
            [new TestResourcesProviderPlugin($applicationName, $resourcePlugins)],
            new UriParser(),
            new RequestResourcePluginFilter(new ConventionResourceFilter([])),
        );
    }
}
