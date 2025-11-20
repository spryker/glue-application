<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueApplication\Http\Response;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\GlueResponseTransfer;
use ReflectionClass;
use Spryker\Glue\GlueApplication\Http\Response\HttpSender;
use Spryker\Glue\GlueApplication\Http\Response\HttpSenderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueApplication
 * @group Http
 * @group Response
 * @group HttpSenderTest
 *
 * Add your own group annotations below this line
 */
class HttpSenderTest extends Unit
{
    /**
     * @var string
     */
    protected const CONNECTION = 'Keep-Alive';

    /**
     * @var string
     */
    protected const TRANSFER_ENCODING = 'chunked';

    /**
     * @var string
     */
    protected const SERVER = 'Nginx';

    /**
     * @var string
     */
    protected const CONTENT = 'body';

    /**
     * @var string
     */
    protected const FORMAT = 'text/html';

    /**
     * @var int
     */
    protected const STATUS_CODE = 200;

    /**
     * @var \SprykerTest\Glue\GlueApplication\GlueApplicationTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testSendResponse(): void
    {
        //Arrange
        $expectedHeaders = ['connection', 'transfer-encoding', 'server'];
        $glueResponseTransfer = (new GlueResponseTransfer())
            ->setContent(static::CONTENT)
            ->setFormat(static::FORMAT)
            ->setHttpStatus(static::STATUS_CODE)
            ->setMeta([
                'Connection' => static::CONNECTION,
                'Transfer-Encoding' => static::TRANSFER_ENCODING,
                'Server' => static::SERVER,
            ]);
        $createMockRequestFlowAwareApiApplication = $this->tester->createRequestFlowAwareApiApplicationMock();
        $createMockRequestFlowAwareApiApplication->expects($this->once())
            ->method('dispatchResponseEvent')
            ->with(
                $this->isInstanceOf(Request::class),
                $this->isInstanceOf(Response::class),
            );
        $requestMock = $this->tester->createRequestMock();
        //Act
        $httpSender = $this->createHttpSender();
        $httpSender->sendResponse(
            $glueResponseTransfer,
            $requestMock,
            $createMockRequestFlowAwareApiApplication,
        );

        $response = $this->getResponse($httpSender);

        // Assert
        foreach ($expectedHeaders as $expectedHeader) {
            $this->assertArrayHasKey($expectedHeader, $response->headers->all());
        }
        $this->assertSame(static::CONTENT, $response->getContent());
        $this->assertSame(static::STATUS_CODE, $response->getStatusCode());
        $this->assertSame(static::SERVER, $response->headers->get('server'));
        $this->assertSame(static::CONNECTION, $response->headers->get('connection'));
        $this->assertSame(static::TRANSFER_ENCODING, $response->headers->get('transfer-encoding'));
        $this->assertSame(static::FORMAT, $response->headers->get('content-type'));
    }

    /**
     * @param \Spryker\Glue\GlueHttp\Response\HttpSender $httpSender
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getResponse(HttpSender $httpSender): Response
    {
        $reflection = new ReflectionClass($httpSender);
        $reflectionProperty = $reflection->getProperty('response');
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($httpSender);
    }

    /**
     * @return \Spryker\Glue\GlueApplication\Http\Response\HttpSenderInterface
     */
    protected function createHttpSender(): HttpSenderInterface
    {
        return new HttpSender(
            new Response(),
        );
    }
}
