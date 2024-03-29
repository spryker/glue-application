<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueApplication\Rest\Response;

use Codeception\Test\Unit;
use Spryker\Glue\GlueApplication\GlueApplicationConfig;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestLinkInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\Page;
use Spryker\Glue\GlueApplication\Rest\Response\ResponsePagination;
use Spryker\Glue\GlueApplication\Rest\Response\ResponsePaginationInterface;
use SprykerTest\Glue\GlueApplication\Stub\RestRequest;
use SprykerTest\Glue\GlueApplication\Stub\RestResponse;

/**
 * @deprecated Will be removed without replacement.
 *
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueApplication
 * @group Rest
 * @group Response
 * @group ResponsePaginationTest
 *
 * Add your own group annotations below this line
 */
class ResponsePaginationTest extends Unit
{
    /**
     * @return void
     */
    public function testBuildPaginationLinksShouldReturnPaginationLinks(): void
    {
        $responsePagination = $this->createResponsePagination();

        $restResponse = (new RestResponse())->createRestResponse();
        $restRequest = (new RestRequest())->createRestRequest();

        $pagination = $responsePagination->buildPaginationLinks($restResponse, $restRequest);

        $this->assertArrayHasKey(RestLinkInterface::LINK_FIRST, $pagination);
        $this->assertArrayHasKey(RestLinkInterface::LINK_LAST, $pagination);
        $this->assertArrayHasKey(RestLinkInterface::LINK_NEXT, $pagination);
        $this->assertArrayHasKey(RestLinkInterface::LINK_PREV, $pagination);
    }

    /**
     * @return void
     */
    public function testBuildPaginationLinksShouldNotReturnNextPaginationLink(): void
    {
        $responsePagination = $this->createResponsePagination();
        $restResponse = (new RestResponse())->createRestResponse();
        $restRequest = (new RestRequest())->createRestRequest();
        $restRequest->setPage((new Page(19, 2)));
        $pagination = $responsePagination->buildPaginationLinks($restResponse, $restRequest);

        $this->assertArrayNotHasKey(RestLinkInterface::LINK_NEXT, $pagination);
    }

    /**
     * @return \Spryker\Glue\GlueApplication\Rest\Response\ResponsePaginationInterface
     */
    public function createResponsePagination(): ResponsePaginationInterface
    {
        return new ResponsePagination(new GlueApplicationConfig());
    }
}
