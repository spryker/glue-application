<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Rest\Request\Data;

/**
 * @deprecated Will be removed without replacement.
 */
interface UserInterface
{
    /**
     * Is a database primary key (id_customer)
     *
     * @return string
     */
    public function getSurrogateIdentifier(): string;

    /**
     * Natural ID (customer reference, email etc)
     *
     * @return string
     */
    public function getNaturalIdentifier(): string;

    /**
     * OAUTH scopes for current user
     *
     * @return array
     */
    public function getScopes(): array;
}
