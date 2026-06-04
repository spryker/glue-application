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
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
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
 *   - resource attributes (populated from the request body using the class map from
 *     {@see GlueApplicationConfig::getResourceTypeAttributesClassMap()}, required for
 *     `getRestRequestValidatorPlugins` validators that read body attributes via
 *     `$restRequest->getResource()->getAttributes()`)
 *
 * Plugins that mutate the request via setters (`setRestUser`, `setPage`, etc.)
 * receive a writable instance — mutations apply to this synthetic object only,
 * never to the upstream Symfony request.
 */
class SyntheticRestRequestBuilder implements SyntheticRestRequestBuilderInterface
{
    protected const string DEFAULT_FORMAT = 'application/vnd.api+json';

    protected const string JSON_API_KEY_DATA = 'data';

    protected const string JSON_API_KEY_ATTRIBUTES = 'attributes';

    public function build(
        Request $httpRequest,
        ?CustomerTransfer $customerTransfer,
        string $resourceShortName,
        ?string $attributesClass = null,
    ): RestRequestInterface {
        $attributesTransfer = $this->buildAttributesTransfer($httpRequest, $attributesClass);
        $resource = new RestResource($resourceShortName, null, $attributesTransfer);
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

    protected function buildAttributesTransfer(Request $httpRequest, ?string $attributesClass): ?AbstractTransfer
    {
        if ($attributesClass === null) {
            return null;
        }

        if (!class_exists($attributesClass) || !is_subclass_of($attributesClass, AbstractTransfer::class)) {
            return null;
        }

        $body = json_decode((string)$httpRequest->getContent(), true);
        $attributes = is_array($body) ? ($body[static::JSON_API_KEY_DATA][static::JSON_API_KEY_ATTRIBUTES] ?? []) : [];

        /** @var \Spryker\Shared\Kernel\Transfer\AbstractTransfer $transfer */
        $transfer = new $attributesClass();
        $transfer->fromArray($attributes, true);

        return $transfer;
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

        // Carry company-user identifiers so legacy validators (e.g. CompanyUserRestUserValidatorPlugin)
        // can recognize an authenticated company user on API Platform-served endpoints.
        // CustomerTransfer.companyUserTransfer is populated upstream by CompanyUserIdentityRequestSubscriber.
        $companyUserTransfer = $customerTransfer->getCompanyUserTransfer();

        if ($companyUserTransfer === null) {
            return $restUserTransfer;
        }

        if ($companyUserTransfer->getIdCompanyUser() !== null) {
            $restUserTransfer->setIdCompanyUser($companyUserTransfer->getIdCompanyUser());
        }

        if ($companyUserTransfer->getFkCompany() !== null) {
            $restUserTransfer->setIdCompany($companyUserTransfer->getFkCompany());
        }

        if ($companyUserTransfer->getFkCompanyBusinessUnit() !== null) {
            $restUserTransfer->setIdCompanyBusinessUnit($companyUserTransfer->getFkCompanyBusinessUnit());
        }

        if ($companyUserTransfer->getUuid() !== null) {
            $restUserTransfer->setUuidCompanyUser($companyUserTransfer->getUuid());
        }

        return $restUserTransfer;
    }
}
