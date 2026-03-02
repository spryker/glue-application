<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Rest\JsonApi;

use ArrayObject;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class RestResource implements RestResourceInterface
{
    /**
     * @var string|null
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array<\Spryker\Glue\GlueApplication\Rest\JsonApi\RestLinkInterface>
     */
    protected $links = [];

    /**
     * @var array
     */
    protected $relationships = [];

    /**
     * @var \Spryker\Shared\Kernel\Transfer\AbstractTransfer|null
     */
    protected $attributes;

    /**
     * @var \Spryker\Shared\Kernel\Transfer\AbstractTransfer|null
     */
    protected $payload;

    public function __construct(string $type, ?string $id = null, ?AbstractTransfer $attributes = null)
    {
        $this->type = $type;
        $this->id = $id;
        $this->attributes = $attributes;
    }

    public function addRelationship(RestResourceInterface $restResource): RestResourceInterface
    {
        if ($restResource->getId()) {
            $this->relationships[$restResource->getType()][$restResource->getId()] = $restResource;

            return $this;
        }

        $this->relationships[$restResource->getType()][] = $restResource;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return array<\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface>
     */
    public function getRelationshipByType(string $type): array
    {
        return $this->relationships[$type];
    }

    public function getRelationships(): array
    {
        return $this->relationships;
    }

    public function addLink(string $name, string $resourceUri, array $meta = []): RestResourceInterface
    {
        $this->links[] = new RestLink($name, $resourceUri, $meta);

        return $this;
    }

    public function hasLink(string $name): bool
    {
        foreach ($this->links as $link) {
            if ($name === $link->getName()) {
                return true;
            }
        }

        return false;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAttributes(): ?AbstractTransfer
    {
        return $this->attributes;
    }

    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @param bool $includeRelations
     *
     * @return array
     */
    public function toArray($includeRelations = true): array
    {
        $response = [
            RestResourceInterface::RESOURCE_TYPE => $this->type,
            RestResourceInterface::RESOURCE_ID => $this->id,
        ];

        if ($this->attributes) {
            $response[RestResourceInterface::RESOURCE_ATTRIBUTES] = $this->transformTransferToArray($this->attributes);
        }

        $response = $this->addLinksDataToResponse($response);

        if (!$includeRelations) {
            return $response;
        }

        $relationships = $this->toArrayRelationships();
        if ($relationships) {
            $response[RestResourceInterface::RESOURCE_RELATIONSHIPS] = $relationships;
        }

        return $response;
    }

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer|null $payload
     *
     * @return $this
     */
    public function setPayload(?AbstractTransfer $payload)
    {
        $this->payload = $payload;

        return $this;
    }

    public function getPayload(): ?AbstractTransfer
    {
        return $this->payload;
    }

    protected function addLinksDataToResponse(array $response): array
    {
        if ($this->links) {
            $response[RestResourceInterface::RESOURCE_LINKS] = [];
            foreach ($this->links as $link) {
                $response[RestResourceInterface::RESOURCE_LINKS] += $link->toArray();
            }
        }

        return $response;
    }

    protected function toArrayRelationships(): array
    {
        $relationships = [];
        foreach ($this->relationships as $type => $typeRelationships) {
            if (!isset($relationships[$type])) {
                $relationships[$type][RestResourceInterface::RESOURCE_DATA] = [];
            }

            foreach ($typeRelationships as $relationship) {
                $relationships[$type][RestResourceInterface::RESOURCE_DATA][] = [
                    RestResourceInterface::RESOURCE_TYPE => $type,
                    RestResourceInterface::RESOURCE_ID => $relationship->getId(),
                ];
            }
        }

        return $relationships;
    }

    /**
     * Used for preventing have empty object instead of empty arrays in the response.
     * Converts transfer object to array.
     * Replaces empty ArrayObjects to empty arrays.
     *
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $transfer
     *
     * @return array
     */
    private function transformTransferToArray(AbstractTransfer $transfer): array
    {
        $transferData = $transfer->toArray(true, true);

        return $this->transformEmptyArrayObjectToArray($transferData);
    }

    private function transformEmptyArrayObjectToArray(array $transferData): array
    {
        foreach ($transferData as $key => $item) {
            if (is_array($item)) {
                $transferData[$key] = $this->transformEmptyArrayObjectToArray($item);
            }
            if (($item instanceof ArrayObject) && count($item) === 0) {
                $transferData[$key] = [];
            }
        }

        return $transferData;
    }
}
