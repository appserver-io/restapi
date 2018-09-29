<?php

/**
 * AppserverIo\RestApi\Handlers\OA2\RequestHandler
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 */

namespace AppserverIo\RestApi\Handlers\OA2;

use AppserverIo\Psr\HttpMessage\Protocol;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
use AppserverIo\RestApi\SerializerInterface;
use AppserverIo\RestApi\Parsers\RequestParserInterface;
use AppserverIo\RestApi\Parsers\ConfigurationParserInterface;
use AppserverIo\RestApi\Handlers\RequestHandlerInterface;
use AppserverIo\RestApi\Wrappers\OperationWrapperInterface;
use AppserverIo\RestApi\Responses\ResponseFactoryInterface;

/**
 * OpenApi 2.0 compatible request handler.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 */
class RequestHandler implements RequestHandlerInterface
{

    /**
     * The request handlers unique API name.
     *
     * @var string
     */
    const API = 'OA2';

    /**
     * The array with the available operations.
     *
     * @var array
     */
    protected $operationWrappers = array();

    /**
     * The application instance.
     *
     * @var \AppserverIo\Psr\Application\ApplicationInterface
     */
    protected $application;

    /**
     * The request parser instance to use.
     *
     * @var \AppserverIo\RestApi\Parsers\RequestParserInterface
     */
    protected $requestParser;

    /**
     * The configuration parser instance to use.
     *
     * @var \AppserverIo\RestApi\Parsers\ConfigurationParserInterface
     */
    protected $configurationParser;

    /**
     * The response factory instance.
     *
     * @var \AppserverIo\RestApi\Responses\ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * The serializer instance.
     *
     * @var \AppserverIo\RestApi\SerializerInterface
     */
    protected $serializer;

    /**
     * Initializes the request handler with the passed instances.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface         $application         The application instance
     * @param \AppserverIo\RestApi\Parsers\RequestParserInterface       $requestParser       The request parser instance
     * @param \AppserverIo\RestApi\Parsers\ConfigurationParserInterface $configurationParser The configuration parser instance
     * @param \AppserverIo\RestApi\Responses\ResponseFactoryInterface   $responseFactory     The response factory instance
     * @param \AppserverIo\RestApi\SerializerInterface                  $serializer          The serializer instance
     */
    public function __construct(
        ApplicationInterface $application,
        RequestParserInterface $requestParser,
        ConfigurationParserInterface $configurationParser,
        ResponseFactoryInterface $responseFactory,
        SerializerInterface $serializer
    ) {
        $this->application = $application;
        $this->requestParser = $requestParser;
        $this->configurationParser = $configurationParser;
        $this->responseFactory = $responseFactory;
        $this->serializer = $serializer;
    }

    /**
     * Returns the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     */
    protected function getApplication()
    {
        return $this->application;
    }

    /**
     * Returns the request parser instance.
     *
     * @return \AppserverIo\RestApi\Parsers\RequestParserInterface The parser instance
     */
    protected function getRequestParser()
    {
        return $this->requestParser;
    }

    /**
     * Returns the configuration parser instance.
     *
     * @return \AppserverIo\RestApi\Parsers\ConfigurationParserInterface The parser instance
     */
    protected function getConfigurationParser()
    {
        return $this->configurationParser;
    }

    /**
     * Returns the response factory instance.
     *
     * @return \AppserverIo\RestApi\Responses\ResponseFactoryInterface The factory instance
     */
    protected function getResponseFactory()
    {
        return $this->responseFactory;
    }

    /**
     * Returns the serializer instance.
     *
     * @return \AppserverIo\RestApi\SerializerInterface The serializer instance
     */
    protected function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * Encodes the passed data, depending on the Accept header of the passed request, and returns it.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest The HTTP servlet request instance
     * @param mixed                                                     $data           The data to serialize
     *
     * @return string The serialized data
     */
    protected function serialize(HttpServletRequestInterface $servletRequest, $data)
    {
        return $this->getSerializer()->serialize($data, $this->getSerializer()->mapHeader($servletRequest));
    }

    /**
     * Returns the content type for the passed servlet request/operation wrapper combination.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest   The HTTP servlet request instance
     * @param \AppserverIo\RestApi\Wrappers\OperationWrapperInterface   $operationWrapper The operation wrapper instance
     *
     * @return string The content type
     * @throws \Exception Is thrown, if the Content-Type defined by the request's Accept header is NOT supported
     */
    protected function produces(HttpServletRequestInterface $servletRequest, OperationWrapperInterface $operationWrapper)
    {

        // query whether or not the requested Content-Type, defined by the request's Accept header is supported
        if (in_array($contentType = $servletRequest->getHeader(Protocol::HEADER_ACCEPT), $operationWrapper->produces())) {
            return $contentType;
        }

        // throw an exception, if not
        throw new \Exception(sprintf('Requested Content-Type "%s", defined by Accept header is NOT supported', $contentType));
    }

    /**
     * Returns the API name, the request handler provides.
     *
     * @return string The API name
     */
    public function getApi()
    {
        return RequestHandler::API;
    }

    /**
     * Initializes the request handler.
     *
     * @return void
     */
    public function init()
    {
        $this->getConfigurationParser()->parse($this);
    }

    /**
     * Adds the passed operation wrapper to the servlet.
     *
     * @param \AppserverIo\RestApi\Wrappers\OperationWrapperInterface $operationWrapper The operation wrapper to add
     *
     * @return void
     */
    public function addOperation(OperationWrapperInterface $operationWrapper)
    {

        // load the operations request method
        $method = strtolower($operationWrapper->getMethod());

        // initialize the array with the operations for that request method, if necessary
        if (!isset($this->operationWrappers[$method])) {
            $this->operationWrappers[$method] = array();
        }

        // append the operation to the array
        array_push($this->operationWrappers[$method], $operationWrapper);
    }

    /**
     * Process the passed request instance.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The HTTP servlet request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The HTTP servlet response instance
     *
     * @return void
     */
    public function handle(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {

        // iterate over the operations for the actual request method
        foreach ($this->operationWrappers[strtolower($servletRequest->getMethod())] as $operationWrapper) {
            // query whether or not, the operation's path matches the request path info
            if ($operationWrapper->match($servletRequest)) {
                try {
                    // load the operations parameters
                    $parameters = $this->getRequestParser()->parse($servletRequest, $operationWrapper);

                    // lookup the bean instance from the application
                    $instance = $this->getApplication()->search($operationWrapper->getLookupName());

                    // inovoke the method
                    $result = call_user_func_array(array($instance, $operationWrapper->getMethodName()), $parameters);

                    // append the result and the headers to the response
                    $servletResponse->addHeader(Protocol::HEADER_CONTENT_TYPE, $this->produces($servletRequest, $operationWrapper));

                    // query whether or not, we've a result
                    if ($result === null) {
                        return;
                    }

                    // if yes, encode it and append it to the body stream
                    $servletResponse->appendBodyStream($this->serialize($servletRequest, $result));
                } catch (\Exception $e) {
                    // log the error
                    \error($e);

                    // load the response wrapper instance
                    $responseWrapper = $operationWrapper->getResponse($e->getCode() ? $e->getCode() : 500);

                    // send the status code and append the response instance
                    $servletResponse->setStatusCode($e->getCode() ? $e->getCode() : 500);
                    $servletResponse->addHeader(Protocol::HEADER_CONTENT_TYPE, $this->produces($servletRequest, $operationWrapper));
                    $servletResponse->appendBodyStream($this->serialize($servletRequest, $this->getResponseFactory()->createResponse($this, $operationWrapper, $e)));
                }
            }
        }
    }
}
