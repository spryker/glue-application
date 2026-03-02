<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Rest\Request\Data;

use Generated\Shared\Transfer\RestUserTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface;
use Symfony\Component\HttpFoundation\Request;

interface RestRequestInterface
{
    public function findParentResourceByType(string $type): ?RestResourceInterface;

    public function getResource(): RestResourceInterface;

    /**
     * @return array<\Spryker\Glue\GlueApplication\Rest\Request\Data\FilterInterface>
     */
    public function getFilters(): array;

    /**
     * @return array<\Spryker\Glue\GlueApplication\Rest\Request\Data\SortInterface>
     */
    public function getSort(): array;

    public function getPage(): ?PageInterface;

    /**
     * @return array<\Spryker\Glue\GlueApplication\Rest\Request\Data\SparseFieldInterface>
     */
    public function getFields(): array;

    public function getMetadata(): MetadataInterface;

    public function getRouteContext(): array;

    /**
     * @return array<\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface>
     */
    public function getParentResources(): array;

    public function getInclude(): array;

    /**
     * @deprecated Use {@link getRestUser()} instead.
     *
     * @return \Spryker\Glue\GlueApplication\Rest\Request\Data\UserInterface|null
     */
    public function getUser(): ?UserInterface;

    /**
     * @deprecated Use {@link setRestUser()} instead.
     *
     * @param string $surrogateIdentifier
     * @param string $naturalIdentifier
     * @param array $scopes
     *
     * @return void
     */
    public function setUser(
        string $surrogateIdentifier,
        string $naturalIdentifier,
        array $scopes = []
    ): void;

    public function getHttpRequest(): Request;

    public function getExcludeRelationship(): bool;

    public function getField(string $resource): SparseFieldInterface;

    public function hasField(string $resource): bool;

    public function hasFilters(string $resource): bool;

    /**
     * @param string $resource
     *
     * @return array<\Spryker\Glue\GlueApplication\Rest\Request\Data\FilterInterface>
     */
    public function getFiltersByResource(string $resource): array;

    /**
     * @return array|null
     */
    public function getAttributesDataFromRequest(): ?array;

    public function setPage(PageInterface $page): void;

    /**
     * @param array<string> $excludeParams
     *
     * @return string
     */
    public function getQueryString(array $excludeParams = []): string;

    /**
     * @param \Generated\Shared\Transfer\RestUserTransfer|null $restUserTransfer
     *
     * @throws \Spryker\Glue\GlueApplication\Rest\Exception\UserAlreadySetException
     *
     * @return void
     */
    public function setRestUser(?RestUserTransfer $restUserTransfer): void;

    public function getRestUser(): ?RestUserTransfer;
}
