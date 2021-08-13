<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Rest;

use Exception;
use Generated\Shared\Transfer\PaginationTransfer;
use Generated\Shared\Transfer\RestErrorCollectionTransfer;
use Generated\Shared\Transfer\SortCollectionTransfer;
use Generated\Shared\Transfer\SortTransfer;
use ReflectionMethod;
use Spryker\Glue\GlueApplication\Controller\ErrorControllerInterface;
use Spryker\Glue\GlueApplication\GlueApplicationConfig;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\SortInterface;
use Spryker\Glue\GlueApplication\Rest\Request\FormattedControllerBeforeActionInterface;
use Spryker\Glue\GlueApplication\Rest\Request\HttpRequestValidatorInterface;
use Spryker\Glue\GlueApplication\Rest\Request\RequestFormatterInterface;
use Spryker\Glue\GlueApplication\Rest\Request\RestRequestValidatorInterface;
use Spryker\Glue\GlueApplication\Rest\Response\ResponseFormatterInterface;
use Spryker\Glue\GlueApplication\Rest\Response\ResponseHeadersInterface;
use Spryker\Glue\GlueApplication\Rest\User\RestUserValidatorInterface;
use Spryker\Glue\GlueApplication\Rest\User\UserProviderInterface;
use Spryker\Glue\Kernel\Controller\AbstractController;
use Spryker\Glue\Kernel\Controller\FormattedAbstractController;
use Spryker\Shared\Log\LoggerTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ControllerFilter implements ControllerFilterInterface
{
    use LoggerTrait;

    /**
     * @var \Spryker\Glue\GlueApplication\Rest\Request\RequestFormatterInterface
     */
    protected $requestFormatter;

    /**
     * @var \Spryker\Glue\GlueApplication\Rest\Response\ResponseFormatterInterface
     */
    protected $responseFormatter;

    /**
     * @var \Spryker\Glue\GlueApplication\Rest\Response\ResponseHeadersInterface
     */
    protected $responseHeaders;

    /**
     * @var \Spryker\Glue\GlueApplication\Rest\Request\HttpRequestValidatorInterface
     */
    protected $httpRequestValidator;

    /**
     * @var \Spryker\Glue\GlueApplication\Rest\Request\RestRequestValidatorInterface
     */
    protected $restRequestValidator;

    /**
     * @var \Spryker\Glue\GlueApplication\Rest\User\RestUserValidatorInterface
     */
    protected $restUserValidator;

    /**
     * @var \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface
     */
    protected $restResourceBuilder;

    /**
     * @var \Spryker\Glue\GlueApplication\Rest\ControllerCallbacksInterface
     */
    protected $controllerCallbacks;

    /**
     * @var \Spryker\Glue\GlueApplication\GlueApplicationConfig
     */
    protected $applicationConfig;

    /**
     * @var \Spryker\Glue\GlueApplication\Rest\User\UserProviderInterface
     */
    protected $userProvider;

    /**
     * @var \Spryker\Glue\GlueApplication\Rest\Request\FormattedControllerBeforeActionInterface
     */
    protected $formattedControllerBeforeAction;

    /**
     * @var \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface
     */
    protected $resourceBuilder;

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\RequestFormatterInterface $requestFormatter
     * @param \Spryker\Glue\GlueApplication\Rest\Response\ResponseFormatterInterface $responseFormatter
     * @param \Spryker\Glue\GlueApplication\Rest\Response\ResponseHeadersInterface $responseHeaders
     * @param \Spryker\Glue\GlueApplication\Rest\Request\HttpRequestValidatorInterface $httpRequestValidator
     * @param \Spryker\Glue\GlueApplication\Rest\Request\RestRequestValidatorInterface $restRequestValidator
     * @param \Spryker\Glue\GlueApplication\Rest\User\RestUserValidatorInterface $restUserValidator
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface $restResourceBuilder
     * @param \Spryker\Glue\GlueApplication\Rest\ControllerCallbacksInterface $controllerCallbacks
     * @param \Spryker\Glue\GlueApplication\GlueApplicationConfig $applicationConfig
     * @param \Spryker\Glue\GlueApplication\Rest\User\UserProviderInterface $userProvider
     * @param \Spryker\Glue\GlueApplication\Rest\Request\FormattedControllerBeforeActionInterface $formattedControllerBeforeAction
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface $resourceBuilder
     */
    public function __construct(
        RequestFormatterInterface $requestFormatter,
        ResponseFormatterInterface $responseFormatter,
        ResponseHeadersInterface $responseHeaders,
        HttpRequestValidatorInterface $httpRequestValidator,
        RestRequestValidatorInterface $restRequestValidator,
        RestUserValidatorInterface $restUserValidator,
        RestResourceBuilderInterface $restResourceBuilder,
        ControllerCallbacksInterface $controllerCallbacks,
        GlueApplicationConfig $applicationConfig,
        UserProviderInterface $userProvider,
        FormattedControllerBeforeActionInterface $formattedControllerBeforeAction,
        RestResourceBuilderInterface $resourceBuilder
    ) {
        $this->requestFormatter = $requestFormatter;
        $this->responseFormatter = $responseFormatter;
        $this->responseHeaders = $responseHeaders;
        $this->httpRequestValidator = $httpRequestValidator;
        $this->restRequestValidator = $restRequestValidator;
        $this->restUserValidator = $restUserValidator;
        $this->restResourceBuilder = $restResourceBuilder;
        $this->controllerCallbacks = $controllerCallbacks;
        $this->applicationConfig = $applicationConfig;
        $this->userProvider = $userProvider;
        $this->formattedControllerBeforeAction = $formattedControllerBeforeAction;
        $this->resourceBuilder = $resourceBuilder;
    }

    /**
     * @param \Spryker\Glue\Kernel\Controller\AbstractController $controller
     * @param string $action
     * @param \Symfony\Component\HttpFoundation\Request $httpRequest
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function filter(AbstractController $controller, string $action, Request $httpRequest): Response
    {
        try {
            $restErrorMessageTransfer = $this->httpRequestValidator->validate($httpRequest);
            if ($restErrorMessageTransfer) {
                return new Response($restErrorMessageTransfer->getDetail(), $restErrorMessageTransfer->getStatus());
            }

            if ($controller instanceof FormattedAbstractController) {
                $restErrorMessageTransfer = $this->formattedControllerBeforeAction->beforeAction($httpRequest);
                if ($restErrorMessageTransfer) {
                    return new Response($restErrorMessageTransfer->getDetail(), $restErrorMessageTransfer->getStatus());
                }

                return $controller->$action($httpRequest);
            }

            $restRequest = $this->requestFormatter->formatRequest($httpRequest);
            $restErrorCollectionTransfer = $this->validateRequest($controller, $httpRequest, $restRequest);
            $restResponse = $this->getRestResponse($restRequest, $restErrorCollectionTransfer, $controller, $action);
            $httpResponse = $this->responseFormatter->format($restResponse, $restRequest);

            return $this->responseHeaders->addHeaders($httpResponse, $restResponse, $restRequest);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     * @param \Generated\Shared\Transfer\RestErrorCollectionTransfer|null $restErrorCollectionTransfer
     * @param \Spryker\Glue\Kernel\Controller\AbstractController $controller
     * @param string $action
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    protected function getRestResponse(
        RestRequestInterface $restRequest,
        ?RestErrorCollectionTransfer $restErrorCollectionTransfer,
        AbstractController $controller,
        string $action
    ): RestResponseInterface {
        if (!$restErrorCollectionTransfer || !$restErrorCollectionTransfer->getRestErrors()->count()) {
            $restRequest = $this->userProvider->setUserToRestRequest($restRequest);
            $restUserValidationRestErrorCollectionTransfer = $this->validateRestUser($restRequest);
            if ($restUserValidationRestErrorCollectionTransfer) {
                return $this->createErrorResponse($restUserValidationRestErrorCollectionTransfer);
            }

            return $this->executeAction($controller, $action, $restRequest);
        }

        return $this->createErrorResponse($restErrorCollectionTransfer);
    }

    /**
     * @param \Spryker\Glue\Kernel\Controller\AbstractController $controller
     * @param string $action
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    protected function processResource(
        AbstractController $controller,
        string $action,
        RestRequestInterface $restRequest
    ): RestResponseInterface {
        $reflectionMethod = new ReflectionMethod($controller, $action);
        $actionParameters = [];
        foreach ($reflectionMethod->getParameters() as $parameter) {
            //TODO: Do parent IDs too
            if ($parameter->getType() && $parameter->getType()->getName() === 'string') {
                $actionParameters[] = $restRequest->getResource()->getId();
                continue;
            }

            $parameterType = $parameter->getClass()->getName();
            if ($parameterType === RestRequestInterface::class) {
                $actionParameters[] = $restRequest;

                continue;
            }

            if (
                str_starts_with($parameterType, 'Generated\Shared\Transfer\\')
                && str_ends_with($parameterType, 'CriteriaTransfer')
            ) {
                $transfer = new $parameterType();

                if (method_exists($transfer, 'setSortCollection')) {
                    $sortCollectionTransfer = new SortCollectionTransfer();
                    foreach ($restRequest->getSort() as $sortField) {
                        $sortCollectionTransfer->addSort(
                            (new SortTransfer())
                                ->setField($sortField->getField())
                                ->setOrderByAsc($sortField->getDirection() === SortInterface::SORT_ASC)
                        );
                    }

                    $transfer->setSortCollection($sortCollectionTransfer);
                }

                if (method_exists($transfer, 'setPagination')) {
                    $paginationTransfer = new PaginationTransfer();
                    if ($restRequest->getPage()) {
                        $paginationTransfer->setOffset($restRequest->getPage()->getOffset())
                            ->setLimit($restRequest->getPage()->getLimit());
                    }

                    $transfer->setPagination($paginationTransfer);
                }

                $transferConditionsGetterFunctionName = '';
                $transferConditionsSetterFunctionName = '';
                $transferFunctions = get_class_methods($transfer);
                foreach ($transferFunctions as $transferFunction) {
                    if (preg_match('/^get.+Conditions$/', $transferFunction)) {
                        $transferConditionsGetterFunctionName = $transferFunction;
                    }

                    if (preg_match('/^set.+Conditions$/', $transferFunction)) {
                        $transferConditionsSetterFunctionName = $transferFunction;
                    }
                }

                if (
                    $transferConditionsGetterFunctionName
                    && $transferConditionsSetterFunctionName
                    && preg_match(
                        '/@return (\\\Generated\\\Shared\\\Transfer\\\.+Transfer)/m',
                        (new ReflectionMethod($transfer, $transferConditionsGetterFunctionName))->getDocComment(),
                        $conditionsTransferName
                    )
                ) {
                    $conditionsTransferName = $conditionsTransferName[1];
                    $conditions = new $conditionsTransferName();
                    foreach ($restRequest->getFilters() as $filters) {
                        foreach ($filters as $filter) {
                            $setterFunction = 'set' . ucwords($filter->getField());
                            if (
                                property_exists($conditions, $filter->getField())
                                && method_exists($conditions, $setterFunction)
                            ) {
                                $conditions->$setterFunction($filter->getValue());

                                continue;
                            }

                            $adderFunction = 'add' . ucwords($filter->getField());

                            if (method_exists($conditions, $adderFunction)) {
                                $conditions->$adderFunction($filter->getValue());
                            }
                        }
                    }

                    if ($restRequest->getResource()->getId() && method_exists($conditions, 'addId')) {
                        $conditions->addId($restRequest->getResource()->getId());
                    }

                    $transfer->$transferConditionsSetterFunctionName($conditions);
                }

                $actionParameters[] = $transfer;

                continue;
            }

            if (str_starts_with($parameterType, 'Generated\Shared\Transfer\\')) {
                $actionParameters[] = $restRequest->getResource()->getAttributes();
            }
        }

        if (
            $reflectionMethod->getReturnType()
            && $reflectionMethod->getReturnType()->getName() === 'array'
        ) {
            preg_match(
                '/@Glue\((\{(?:(?>[^{}"\'\/]+)|(?>"(?:(?>[^\\\\"]+)|\\\\.)*")|(?>\'(?:(?>[^\\\\\']+)|\\\\.)*\')|(?>\/\/.*\n)|(?>\/\*.*?\*\/)|(?-1))*\})/m',
                $reflectionMethod->getDocComment(),
                $glueJsonDoc
            );

            $glueDoc = $glueJsonDoc ? json_decode(str_replace('*', '', $glueJsonDoc[1]), true) : null;
            if ($glueDoc && isset($glueDoc['type'])) {
                $transferIdGetterFunction = '';
                if (isset($glueDoc['idAttribute'])) {
                    $transferIdGetterFunction = 'get' . ucwords($glueDoc['idAttribute']);
                }
                $transfers = $controller->$action(...$actionParameters);
                $restResponse = $this->resourceBuilder->createRestResponse();

                foreach ($transfers as $transfer) {
                    $restResponse->addResource(
                        $this->resourceBuilder->createRestResource(
                            $glueDoc['type'],
                            $transferIdGetterFunction ? $transfer->$transferIdGetterFunction() : null,
                            $transfer
                        )
                    );
                }

                return $restResponse;
            }
        }

        return $controller->$action(...$actionParameters);
    }

    /**
     * @param \Spryker\Glue\Kernel\Controller\AbstractController $controller
     * @param string $action
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    protected function executeAction(
        AbstractController $controller,
        string $action,
        RestRequestInterface $restRequest
    ): RestResponseInterface {
        $this->controllerCallbacks->beforeAction($action, $restRequest);

        if ($controller instanceof ErrorControllerInterface) {
            $restResponse = $controller->$action();
        } else {
            $restResponse = $this->processResource($controller, $action, $restRequest);
        }

        $this->controllerCallbacks->afterAction($action, $restRequest, $restResponse);

        return $restResponse;
    }

    /**
     * @param \Exception $exception
     *
     * @throws \Exception
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleException(Exception $exception): Response
    {
        if ($this->applicationConfig->getIsRestDebugEnabled()) {
            throw $exception;
        }

        $this->logException($exception);

        return new Response(
            Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    /**
     * @param \Exception $exception
     *
     * @return void
     */
    protected function logException(Exception $exception): void
    {
        $this->getLogger()->error($exception->getMessage(), ['trace' => $exception->getTraceAsString()]);
    }

    /**
     * @param \Spryker\Glue\Kernel\Controller\AbstractController $controller
     * @param \Symfony\Component\HttpFoundation\Request $httpRequest
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return \Generated\Shared\Transfer\RestErrorCollectionTransfer|null
     */
    protected function validateRequest(AbstractController $controller, Request $httpRequest, RestRequestInterface $restRequest): ?RestErrorCollectionTransfer
    {
        $restErrorCollectionTransfer = null;
        if (!$controller instanceof ErrorControllerInterface) {
            $restErrorCollectionTransfer = $this->restRequestValidator->validate($httpRequest, $restRequest);
        }

        return $restErrorCollectionTransfer;
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return \Generated\Shared\Transfer\RestErrorCollectionTransfer|null
     */
    protected function validateRestUser(RestRequestInterface $restRequest): ?RestErrorCollectionTransfer
    {
        return $this->restUserValidator->validate($restRequest);
    }

    /**
     * @param \Generated\Shared\Transfer\RestErrorCollectionTransfer $restErrorCollectionTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    protected function createErrorResponse(RestErrorCollectionTransfer $restErrorCollectionTransfer): RestResponseInterface
    {
        $restResponse = $this->restResourceBuilder->createRestResponse();
        foreach ($restErrorCollectionTransfer->getRestErrors() as $restErrorMessageTransfer) {
            $restResponse->addError($restErrorMessageTransfer);
        }

        return $restResponse;
    }
}
