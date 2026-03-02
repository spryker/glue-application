<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueApplication\Builder\Request;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\PaginationTransfer;
use Spryker\Glue\GlueApplication\Builder\Request\PaginationParameterRequestBuilder;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueApplication
 * @group Builder
 * @group Request
 * @group PaginationParameterRequestBuilderTest
 * Add your own group annotations below this line
 */
class PaginationParameterRequestBuilderTest extends Unit
{
    /**
     * @var string
     */
    protected const QUERY_PAGINATION = 'page';

    /**
     * @var string
     */
    protected const PAGINATION_OFFSET = 'offset';

    /**
     * @var string
     */
    protected const PAGINATION_LIMIT = 'limit';

    public function testNoPagination(): void
    {
        //Arrange
        $glueRequest = new GlueRequestTransfer();

        //Act
        $builder = new PaginationParameterRequestBuilder();
        $result = $builder->build($glueRequest);

        //Assert
        $this->assertNull($result->getPagination());
    }

    public function testEmptyPagination(): void
    {
        //Act
        $result = $this->buildRequest(null, null);

        //Assert
        $this->assertNull($result->getPagination());
    }

    public function testMissingOffset(): void
    {
        //Act
        $result = $this->buildRequest(null, 10);

        //Assert
        $this->assertNull($result->getPagination());
    }

    public function testMissingLimit(): void
    {
        //Act
        $result = $this->buildRequest(0, null);

        //Assert
        $this->assertNull($result->getPagination());
    }

    public function testCompletePage(): void
    {
        //Act
        $result = $this->buildRequest(0, 10);

        //Assert
        $this->assertInstanceOf(PaginationTransfer::class, $result->getPagination());
        $this->assertSame(0, $result->getPagination()->getOffset());
        $this->assertSame(10, $result->getPagination()->getLimit());
    }

    protected function buildRequest(?int $offset = null, ?int $limit = null): GlueRequestTransfer
    {
        $glueRequest = new GlueRequestTransfer();
        $glueRequest->setQueryFields([
            static::QUERY_PAGINATION => [
                static::PAGINATION_OFFSET => $offset,
                static::PAGINATION_LIMIT => $limit,
            ],
        ]);

        $builder = new PaginationParameterRequestBuilder();

        return $builder->build($glueRequest);
    }
}
