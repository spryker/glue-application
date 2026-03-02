<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Rest\Request\Data;

use Generated\Shared\Transfer\RestUserTransfer;
use Spryker\Glue\GlueApplication\Rest\Exception\UserAlreadySetException;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface;
use Symfony\Component\HttpFoundation\Request;

class RestRequest implements RestRequestInterface
{
    /**
     * @var \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface
     */
    protected $resource;

    /**
     * @var array<\Spryker\Glue\GlueApplication\Rest\Request\Data\SortInterface>
     */
    protected $sort;

    /**
     * @var \Spryker\Glue\GlueApplication\Rest\Request\Data\PageInterface|null
     */
    protected $page;

    /**
     * @var array<\Spryker\Glue\GlueApplication\Rest\Request\Data\SparseFieldInterface>
     */
    protected $fields = [];

    /**
     * @var bool
     */
    protected $excludeRelationship = false;

    /**
     * @var \Spryker\Glue\GlueApplication\Rest\Request\Data\MetadataInterface
     */
    protected $metadata;

    /**
     * @var array
     */
    protected $routeContext = [];

    /**
     * @var array<\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface>
     */
    protected $parentResources = [];

    /**
     * @var array
     */
    protected $include = [];

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @deprecated Use $restUser instead.
     *
     * @var \Spryker\Glue\GlueApplication\Rest\Request\Data\UserInterface|null
     */
    protected $user;

    /**
     * @var \Generated\Shared\Transfer\RestUserTransfer|null
     */
    protected $restUser;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $httpRequest;

    public function __construct(
        RestResourceInterface $resource,
        Request $httpRequest,
        MetadataInterface $metadata,
        array $filters,
        array $sort,
        ?PageInterface $page,
        array $routeContext,
        array $parentResources,
        array $include,
        array $fields,
        bool $excludeRelationship,
        ?UserInterface $user = null
    ) {
        $this->resource = $resource;
        $this->filters = $filters;
        $this->sort = $sort;
        $this->page = $page;
        $this->fields = $fields;
        $this->metadata = $metadata;
        $this->routeContext = $routeContext;
        $this->parentResources = $parentResources;
        $this->include = $include;
        $this->user = $user;
        $this->httpRequest = $httpRequest;
        $this->excludeRelationship = $excludeRelationship;
    }

    public function findParentResourceByType(string $type): ?RestResourceInterface
    {
        if (!isset($this->parentResources[$type])) {
            return null;
        }

        return $this->parentResources[$type];
    }

    public function getResource(): RestResourceInterface
    {
        return $this->resource;
    }

    /**
     * @return array<\Spryker\Glue\GlueApplication\Rest\Request\Data\FilterInterface>
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    public function hasFilters(string $resource): bool
    {
        return isset($this->filters[$resource]);
    }

    public function getFiltersByResource(string $resource): array
    {
        return $this->filters[$resource];
    }

    /**
     * @return array<\Spryker\Glue\GlueApplication\Rest\Request\Data\SortInterface>
     */
    public function getSort(): array
    {
        return $this->sort;
    }

    public function getPage(): ?PageInterface
    {
        return $this->page;
    }

    /**
     * @return array<\Spryker\Glue\GlueApplication\Rest\Request\Data\SparseFieldInterface>
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function getField(string $resource): SparseFieldInterface
    {
        return $this->fields[$resource];
    }

    public function hasField(string $resource): bool
    {
        return isset($this->fields[$resource]);
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    public function getRouteContext(): array
    {
        return $this->routeContext;
    }

    /**
     * @return array<\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface>
     */
    public function getParentResources(): array
    {
        return $this->parentResources;
    }

    public function getInclude(): array
    {
        return $this->include;
    }

    /**
     * @deprecated Use {@link getRestUser()} instead.
     *
     * @return \Spryker\Glue\GlueApplication\Rest\Request\Data\UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        trigger_error(sprintf('Use %s::getRestUser() instead.', static::class), E_USER_DEPRECATED);

        if (!$this->user && $this->restUser) {
            $this->user = new User(
                (string)$this->restUser->getSurrogateIdentifier(),
                (string)$this->restUser->getNaturalIdentifier(),
                $this->restUser->getScopes(),
            );
        }

        return $this->user;
    }

    /**
     * @deprecated Use {@link setRestUser()} instead.
     *
     * @param string $surrogateIdentifier
     * @param string $naturalIdentifier
     * @param array $scopes
     *
     * @throws \Spryker\Glue\GlueApplication\Rest\Exception\UserAlreadySetException
     *
     * @return void
     */
    public function setUser(
        string $surrogateIdentifier,
        string $naturalIdentifier,
        array $scopes = []
    ): void {
        trigger_error(sprintf('Use %s setRestUser() instead.', static::class), E_USER_DEPRECATED);

        if ($this->user || $this->restUser) {
            throw new UserAlreadySetException('Rest request object already have user set.');
        }

        $this->user = new User($surrogateIdentifier, $naturalIdentifier, $scopes);
        $this->restUser = (new RestUserTransfer())
            ->setSurrogateIdentifier((int)$surrogateIdentifier)
            ->setNaturalIdentifier($naturalIdentifier)
            ->setScopes($scopes);
    }

    /**
     * @param \Generated\Shared\Transfer\RestUserTransfer|null $restUserTransfer
     *
     * @throws \Spryker\Glue\GlueApplication\Rest\Exception\UserAlreadySetException
     *
     * @return void
     */
    public function setRestUser(?RestUserTransfer $restUserTransfer): void
    {
        if ($this->restUser) {
            throw new UserAlreadySetException('Rest request object already have user set.');
        }

        $this->restUser = $restUserTransfer;
    }

    public function getRestUser(): ?RestUserTransfer
    {
        return $this->restUser;
    }

    public function getHttpRequest(): Request
    {
        return $this->httpRequest;
    }

    public function getExcludeRelationship(): bool
    {
        return $this->excludeRelationship;
    }

    /**
     * @return array|null
     */
    public function getAttributesDataFromRequest(): ?array
    {
        if (!isset($this->httpRequest->attributes->get(RestResourceInterface::RESOURCE_DATA)[RestResourceInterface::RESOURCE_ATTRIBUTES])) {
            return null;
        }

        return $this->httpRequest->attributes->get(RestResourceInterface::RESOURCE_DATA)[RestResourceInterface::RESOURCE_ATTRIBUTES];
    }

    public function setPage(PageInterface $page): void
    {
        $this->page = $page;
    }

    /**
     * @param array<string> $excludeParams
     *
     * @return string
     */
    public function getQueryString(array $excludeParams = []): string
    {
        $queryParams = $this->getHttpRequest()->query->all();
        $queryParams = $this->filterQueryParams($queryParams, $excludeParams);

        return urldecode(http_build_query($queryParams));
    }

    /**
     * @param array $queryParams
     * @param array<string> $excludeParams
     *
     * @return array
     */
    protected function filterQueryParams(array $queryParams, array $excludeParams): array
    {
        foreach ($excludeParams as $param) {
            unset($queryParams[$param]);
        }

        return $queryParams;
    }
}
