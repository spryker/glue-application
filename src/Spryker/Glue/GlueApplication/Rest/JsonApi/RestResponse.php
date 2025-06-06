<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Rest\JsonApi;

use Generated\Shared\Transfer\RestErrorMessageTransfer;

class RestResponse implements RestResponseInterface
{
    /**
     * @var array<\Spryker\Glue\GlueApplication\Rest\JsonApi\RestLinkInterface>
     */
    protected $links = [];

    /**
     * @var array<\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface>
     */
    protected $resources = [];

    /**
     * @var array<\Generated\Shared\Transfer\RestErrorMessageTransfer>
     */
    protected $errors = [];

    /**
     * @var int
     */
    protected $totalItems = 0;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var int
     */
    protected $status = 0;

    /**
     * @param int $totalItems
     * @param int $limit
     */
    public function __construct(int $totalItems = 0, int $limit = 0)
    {
        $this->totalItems = $totalItems;
        $this->limit = $limit;
    }

    /**
     * @param \Generated\Shared\Transfer\RestErrorMessageTransfer $error
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function addError(RestErrorMessageTransfer $error): RestResponseInterface
    {
        $this->errors[] = $error;

        return $this;
    }

    /**
     * @return array<\Generated\Shared\Transfer\RestErrorMessageTransfer>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param string $name
     * @param string $uri
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function addLink(string $name, string $uri): RestResponseInterface
    {
        $this->links[$name] = new RestLink($name, $uri);

        return $this;
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface $restResource
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function addResource(RestResourceInterface $restResource): RestResponseInterface
    {
        $this->resources[] = $restResource;

        return $this;
    }

    /**
     * @return array
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @return array<\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface>
     */
    public function getResources(): array
    {
        return $this->resources;
    }

    /**
     * @return int
     */
    public function getTotals(): int
    {
        return $this->totalItems;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function setStatus(int $status): RestResponseInterface
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function addHeader(string $key, string $value): RestResponseInterface
    {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
