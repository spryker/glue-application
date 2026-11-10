<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerTest\Glue\GlueApplication\Compatibility\EventSubscriber;

use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use Codeception\Test\Unit;
use Spryker\Glue\GlueApplication\Compatibility\EventSubscriber\GlueRestPluginStackSubscriber;
use Spryker\Glue\GlueApplication\Compatibility\EventSubscriber\LegacyPluginBridgeSubscriber;
use Spryker\Glue\GlueApplication\Compatibility\RequestBuilder\SyntheticRestRequestBuilderInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueApplication
 * @group Compatibility
 * @group EventSubscriber
 * @group LegacyPluginBridgeSubscriberTest
 * Add your own group annotations below this line
 */
class LegacyPluginBridgeSubscriberTest extends Unit
{
    public function testGivenTheDeprecatedSubscriberWhenItIsInstantiatedThenItIsAGlueRestPluginStackSubscriber(): void
    {
        // Act
        $legacyPluginBridgeSubscriber = new LegacyPluginBridgeSubscriber(
            $this->createMock(ResourceMetadataCollectionFactoryInterface::class),
            $this->createMock(SyntheticRestRequestBuilderInterface::class),
        );

        // Assert
        $this->assertInstanceOf(GlueRestPluginStackSubscriber::class, $legacyPluginBridgeSubscriber);
    }
}
