<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Router\ResourceRouter;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResourceTransfer;
use Spryker\Glue\GlueApplication\Exception\AmbiguousResourceException;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface;

class RequestResourcePluginFilter implements RequestResourcePluginFilterInterface
{
    /**
     * @var \Spryker\Glue\GlueApplication\Router\ResourceRouter\ConventionResourceFilterInterface
     */
    protected ConventionResourceFilterInterface $conventionResourceFilter;

    /**
     * @param \Spryker\Glue\GlueApplication\Router\ResourceRouter\ConventionResourceFilterInterface $conventionResourceFilter
     */
    public function __construct(ConventionResourceFilterInterface $conventionResourceFilter)
    {
        $this->conventionResourceFilter = $conventionResourceFilter;
    }

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     * @param array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface> $resourcePlugins
     * @param \Generated\Shared\Transfer\GlueResourceTransfer $glueResourceTransfer
     *
     * @throws \Spryker\Glue\GlueApplication\Exception\AmbiguousResourceException
     *
     * @return \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface|null
     */
    public function filterResourcePlugins(
        GlueRequestTransfer $glueRequestTransfer,
        array $resourcePlugins,
        GlueResourceTransfer $glueResourceTransfer
    ): ?ResourceInterface {
        if (!$glueRequestTransfer->getResource()) {
            return null;
        }

        $filteredResourcePlugins = $this->filterByResource($resourcePlugins, $glueResourceTransfer);
        $filteredResourcePlugins = $this->conventionResourceFilter->filter($glueRequestTransfer, $filteredResourcePlugins);

        if (count($filteredResourcePlugins) > 1) {
            throw new AmbiguousResourceException(sprintf(
                'More than one %s plugin was found to match',
                ResourceInterface::class,
            ));
        }

        return count($filteredResourcePlugins) !== 0 ? current($filteredResourcePlugins) : null;
    }

    /**
     * @param array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface> $resourcePlugins
     * @param \Generated\Shared\Transfer\GlueResourceTransfer $glueResourceTransfer
     *
     * @return array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface>
     */
    protected function filterByResource(array $resourcePlugins, GlueResourceTransfer $glueResourceTransfer): array
    {
        return array_filter(
            $resourcePlugins,
            function (ResourceInterface $resourcePlugin) use ($glueResourceTransfer): bool {
                return $glueResourceTransfer->getResourceName() === $resourcePlugin->getType();
            },
        );
    }
}
