<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Http\Response;

use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\GlueApplication\ApiApplication\Type\RequestFlowAwareApiApplication;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpSender implements HttpSenderInterface
{
    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function __construct(protected Response $response)
    {
    }

    /**
     * @param \Generated\Shared\Transfer\GlueResponseTransfer $glueResponseTransfer
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Spryker\Glue\GlueApplication\ApiApplication\Type\RequestFlowAwareApiApplication $apiApplication
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sendResponse(
        GlueResponseTransfer $glueResponseTransfer,
        Request $request,
        RequestFlowAwareApiApplication $apiApplication,
    ): Response {
        $this->response->setContent($glueResponseTransfer->getContent());
        $this->response->headers->add($glueResponseTransfer->getMeta());
        $this->response->setStatusCode($glueResponseTransfer->getHttpStatusOrFail());
        if ($glueResponseTransfer->getFormat()) {
            $this->response->headers->set('Content-Type', $glueResponseTransfer->getFormat());
        }

        $apiApplication->dispatchResponseEvent($request, $this->response);

        return $this->response->send();
    }
}
