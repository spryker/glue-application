<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Plugin\GlueApi;

use Generated\Shared\Transfer\ApiApplicationContextTransfer;
use Generated\Shared\Transfer\RestErrorMessageTransfer;
use Spryker\Glue\GlueApiExtension\GlueApiApplicationPluginInterface;
use Spryker\Glue\Kernel\AbstractPlugin;
use Spryker\Shared\Application\ApplicationInterface;

/**
 * @method \Spryker\Glue\GlueApplication\GlueApplicationConfig getConfig()
 * @method \Spryker\Glue\GlueApplication\GlueApplicationFactory getFactory()
 */
class StorefrontGlueApiApplicationPlugin extends AbstractPlugin implements GlueApiApplicationPluginInterface
{
    /**
     * @param \Generated\Shared\Transfer\ApiApplicationContextTransfer $apiApplicationContextTransfer
     *
     * @return mixed
     */
    public function isApplicable(ApiApplicationContextTransfer $apiApplicationContextTransfer)
    {
        return (bool)preg_match('/glue/', $apiApplicationContextTransfer->getHost());
    }

    /**
     * @return \Spryker\Shared\Application\ApplicationInterface
     */
    public function getApplication(): ApplicationInterface
    {
        return $this->getFactory()->createApplication();
    }
}
