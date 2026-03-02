<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Rest\User;

use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\RestUserFinderPluginInterface>
     */
    protected $restUserFinderPlugins;

    /**
     * @param array<\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\RestUserFinderPluginInterface> $restUserFinderPlugins
     */
    public function __construct(array $restUserFinderPlugins)
    {
        $this->restUserFinderPlugins = $restUserFinderPlugins;
    }

    public function setUserToRestRequest(RestRequestInterface $restRequest): RestRequestInterface
    {
        if ($restRequest->getRestUser()) {
            return $restRequest;
        }

        foreach ($this->restUserFinderPlugins as $restUserFinderPlugin) {
            $restUserTransfer = $restUserFinderPlugin->findUser($restRequest);
            if ($restUserTransfer) {
                $restRequest->setRestUser($restUserTransfer);

                return $restRequest;
            }
        }

        return $restRequest;
    }
}
