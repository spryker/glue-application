<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication;

use Spryker\Glue\GlueApplication\Rest\RequestConstantsInterface;
use Spryker\Glue\Kernel\AbstractBundleConfig;
use Spryker\Shared\GlueApplication\GlueApplicationConstants;

class GlueApplicationConfig extends AbstractBundleConfig
{
    /**
     * @deprecated Will be removed without replacement.
     *
     * @var string
     */
    public const COLLECTION_IDENTIFIER_CURRENT_USER = 'mine';

    /**
     * @var string
     */
    public const RESOURCE_NOT_FOUND = '007';

    /**
     * @var string
     */
    public const RESOURCE_NOT_FOUND_MESSAGE = 'Resource not found';

    /**
     * @var string
     */
    public const ROUTE_NOT_FOUND = '008';

    /**
     * @var string
     */
    public const ROUTE_NOT_FOUND_MESSAGE = 'Resource not found';

    /**
     * @var string
     */
    public const METHOD_NOT_EXIST = '009';

    /**
     * @var string
     */
    public const METHOD_NOT_EXIST_MESSAGE = 'Route not found';

    /**
     * @deprecated Will be removed without replacement.
     *
     * @var string
     */
    protected const HEADER_X_FRAME_OPTIONS_VALUE = 'SAMEORIGIN';

    /**
     * @deprecated Will be removed without replacement.
     *
     * @var string
     */
    protected const HEADER_CONTENT_SECURITY_POLICY_VALUE = 'frame-ancestors \'self\'';

    /**
     * @deprecated Will be removed without replacement.
     *
     * @var string
     */
    protected const HEADER_X_CONTENT_TYPE_OPTIONS_VALUE = 'nosniff';

    /**
     * @deprecated Will be removed without replacement.
     *
     * @var string
     */
    protected const HEADER_X_XSS_PROTECTION_VALUE = '1; mode=block';

    /**
     * @deprecated Will be removed without replacement.
     *
     * @var string
     */
    protected const HEADER_REFERRER_POLICY_VALUE = 'same-origin';

    /**
     * @deprecated Will be removed without replacement.
     *
     * @var string
     */
    protected const HEADER_PERMISSIONS_POLICY_VALUE = '';

    /**
     * @deprecated Will be removed without replacement.
     *
     * @var bool
     */
    public const VALIDATE_REQUEST_HEADERS = true;

    /**
     * @api
     *
     * @deprecated Will be removed without replacement.
     *
     * Specification:
     *  - Enables or disables request header validation.
     *
     * @return bool
     */
    public function getValidateRequestHeaders(): bool
    {
        return static::VALIDATE_REQUEST_HEADERS;
    }

    /**
     * @api
     *
     * @deprecated Will be removed without replacement.
     *
     * Specification:
     *  - Domain name of glue application to build API links.
     *
     * @return string
     */
    public function getGlueDomainName(): string
    {
        return $this->get(GlueApplicationConstants::GLUE_APPLICATION_DOMAIN);
    }

    /**
     * @api
     *
     * @deprecated Will be removed without replacement.
     *
     * Specification:
     *  - Indicates whether debug of rest is enabled.
     *
     * @return bool
     */
    public function getIsRestDebugEnabled(): bool
    {
        return $this->get(GlueApplicationConstants::GLUE_APPLICATION_REST_DEBUG, false);
    }

    /**
     * @api
     *
     * @deprecated Will be removed without replacement.
     *
     * Specification:
     *  - Specifies a URI that may access the resources.
     *
     * @return string
     */
    public function getCorsAllowOrigin(): string
    {
        return $this->get(GlueApplicationConstants::GLUE_APPLICATION_CORS_ALLOW_ORIGIN, '');
    }

    /**
     * @api
     *
     * @deprecated Will be removed without replacement.
     *
     * Specification:
     *  - List of allowed CORS headers.
     *
     * @return array<string>
     */
    public function getCorsAllowedHeaders(): array
    {
        return [
            RequestConstantsInterface::HEADER_ACCEPT,
            RequestConstantsInterface::HEADER_CONTENT_TYPE,
            RequestConstantsInterface::HEADER_CONTENT_LANGUAGE,
            RequestConstantsInterface::HEADER_ACCEPT_LANGUAGE,
            RequestConstantsInterface::HEADER_AUTHORIZATION,
        ];
    }

    /**
     * @api
     *
     * @deprecated Will be removed without replacement.
     *
     * @return array<string, string>
     */
    public function getSecurityHeaders(): array
    {
        return [
            'X-Frame-Options' => static::HEADER_X_FRAME_OPTIONS_VALUE,
            'Content-Security-Policy' => static::HEADER_CONTENT_SECURITY_POLICY_VALUE,
            'X-Content-Type-Options' => static::HEADER_X_CONTENT_TYPE_OPTIONS_VALUE,
            'X-XSS-Protection' => static::HEADER_X_XSS_PROTECTION_VALUE,
            'Referrer-Policy' => static::HEADER_REFERRER_POLICY_VALUE,
            'Permissions-policy' => static::HEADER_PERMISSIONS_POLICY_VALUE,
        ];
    }

    /**
     * @api
     *
     * @deprecated Will be removed without replacement.
     *
     * Specification:
     *  - Indicates whether all relationships should be included in response by default.
     *
     * @return bool
     */
    public function isEagerRelationshipsLoadingEnabled(): bool
    {
        return true;
    }

    /**
     * @api
     *
     * @deprecated Will be removed without replacement.
     *
     * @return bool
     */
    public function isDebugModeEnabled(): bool
    {
        return $this->get(GlueApplicationConstants::ENABLE_APPLICATION_DEBUG, false);
    }

    /**
     * @api
     *
     * @deprecated Will be removed without replacement.
     *
     * Specification:
     * - Overwrite this to true if API version resolving should happen to all endpoints via the first part of the path
     * - e.g /1/resource1 or /v1/resource2 instead of header value
     *
     * @return bool
     */
    public function getPathVersionResolving(): bool
    {
        return false;
    }

    /**
     * @api
     *
     * @deprecated Will be removed without replacement.
     *
     * Specification:
     * - Set this to the value you want to be the prefix of the version in the URL (if any)
     * - In the default setting, it will not exist, but if it is set to "v" then all versionable resources will have
     * - a "v" as a prefix to their version in the URL. e.g. /v1/resource
     *
     * @return string
     */
    public function getPathVersionPrefix(): string
    {
        return '';
    }

    /**
     * @api
     *
     * @deprecated Will be removed without replacement.
     *
     * Specification:
     * - Official semver regex for matching a semver version, but removed the requirement for patch or minor version
     * - for easier versioning of APIs. API versions do not have patch versions since patches do not change the response type
     *
     * - To overwrite this smoothly, please add a named capturing group called "fullVersion" to your regex that contains
     * - your full semVer version (e.g 1.1 or 1). Otherwise, the first capture group will be taken as full version number
     *
     * @see https://semver.org/#is-there-a-suggested-regular-expression-regex-to-check-a-semver-string
     *
     * @return string
     */
    public function getApiVersionResolvingRegex(): string
    {
        return '/^(?P<fullVersion>(0|[1-9]\d*)(\.(0|[1-9]\d*))?)$/';
    }
}
