<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Validator;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueRequestValidationTransfer;
use Spryker\Glue\GlueApplication\ApiApplication\Type\RequestFlowAwareApiApplication;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ConventionPluginInterface;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface;

interface RequestValidatorInterface
{
    public function validate(
        GlueRequestTransfer $glueRequestTransfer,
        RequestFlowAwareApiApplication $apiApplication,
        ?ConventionPluginInterface $apiConventionPlugin
    ): GlueRequestValidationTransfer;

    public function validateAfterRouting(
        GlueRequestTransfer $glueRequestTransfer,
        ResourceInterface $resource,
        RequestFlowAwareApiApplication $apiApplication,
        ?ConventionPluginInterface $apiConventionPlugin
    ): GlueRequestValidationTransfer;
}
