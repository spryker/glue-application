<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Rest\Serialize;

use Spryker\Glue\GlueApplication\Rest\Request\Data\MetadataInterface;
use Spryker\Glue\GlueApplication\Serialize\Encoder\EncoderInterface;

class EncoderMatcher implements EncoderMatcherInterface
{
    /**
     * @var string
     */
    public const DEFAULT_FORMAT = 'json';

    /**
     * @var array<\Spryker\Glue\GlueApplication\Serialize\Encoder\EncoderInterface>
     */
    protected $encoders;

    public function __construct(array $encoders = [])
    {
        $this->encoders = $encoders;
    }

    public function match(MetadataInterface $metadata): ?EncoderInterface
    {
        if (!$metadata->getAcceptFormat()) {
            return $this->encoders[static::DEFAULT_FORMAT];
        }

        foreach ($this->encoders as $format => $encoder) {
            if (strcasecmp($format, $metadata->getAcceptFormat()) === 0) {
                return $encoder;
            }
        }

        return null;
    }
}
