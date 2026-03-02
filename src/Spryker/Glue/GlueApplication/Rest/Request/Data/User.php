<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Rest\Request\Data;

class User implements UserInterface
{
    /**
     * @var string
     */
    protected $surrogateIdentifier;

    /**
     * @var string
     */
    protected $naturalIdentifier;

    /**
     * @var array
     */
    protected $scopes;

    public function __construct(string $surrogateIdentifier, string $naturalIdentifier, array $scopes = [])
    {
        $this->surrogateIdentifier = $surrogateIdentifier;
        $this->naturalIdentifier = $naturalIdentifier;
        $this->scopes = $scopes;
    }

    public function getSurrogateIdentifier(): string
    {
        return $this->surrogateIdentifier;
    }

    public function getNaturalIdentifier(): string
    {
        return $this->naturalIdentifier;
    }

    public function getScopes(): array
    {
        return $this->scopes;
    }
}
