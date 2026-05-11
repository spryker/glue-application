<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\GlueApplication\Compatibility\RequestBuilder;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\RestUserTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResource;
use Spryker\Glue\GlueApplication\Rest\Request\Data\Metadata;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequest;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Constructs a minimal {@see RestRequestInterface} from an API Platform Symfony
 * request so that legacy plugins that consume `RestRequestInterface` can still
 * run for endpoints migrated to API Platform.
 *
 * Only the fields the existing legacy plugin chain reads are populated:
 *   - resource type (API Platform shortName)
 *   - rest user (customer surrogate identifier + customer reference)
 *   - the original Symfony request
 *   - default empty filter / sort / include collections
 *
 * Plugins that mutate the request via setters (`setRestUser`, `setPage`, etc.)
 * receive a writable instance — mutations apply to this synthetic object only,
 * never to the upstream Symfony request.
 */
class SyntheticRestRequestBuilder implements SyntheticRestRequestBuilderInterface
{
    protected const string DEFAULT_FORMAT = 'application/vnd.api+json';

    public function build(
        Request $httpRequest,
        ?CustomerTransfer $customerTransfer,
        string $resourceShortName
    ): RestRequestInterface {
        $resource = new RestResource($resourceShortName);
        $metadata = new Metadata(
            static::DEFAULT_FORMAT,
            static::DEFAULT_FORMAT,
            $httpRequest->getMethod(),
            (string)$httpRequest->getLocale(),
            true,
        );

        $restRequest = new RestRequest(
            $resource,
            $httpRequest,
            $metadata,
            [],
            [],
            null,
            [],
            [],
            [],
            [],
            false,
            null,
        );

        if ($customerTransfer !== null) {
            $restRequest->setRestUser($this->buildRestUserTransfer($customerTransfer));
        }

        return $restRequest;
    }

    protected function buildRestUserTransfer(CustomerTransfer $customerTransfer): RestUserTransfer
    {
        $restUserTransfer = new RestUserTransfer();

        if ($customerTransfer->getIdCustomer() !== null) {
            $restUserTransfer->setSurrogateIdentifier($customerTransfer->getIdCustomer());
        }

        if ($customerTransfer->getCustomerReference() !== null) {
            $restUserTransfer->setNaturalIdentifier($customerTransfer->getCustomerReference());
        }

        return $restUserTransfer;
    }
}
