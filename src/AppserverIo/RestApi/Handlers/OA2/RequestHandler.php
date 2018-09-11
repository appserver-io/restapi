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

use JMS\Serializer\SerializerBuilder;
use AppserverIo\Psr\HttpMessage\Protocol;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
use AppserverIo\RestApi\Utils\FormatKeys;
use AppserverIo\RestApi\Parsers\RequestParserInterface;
use AppserverIo\RestApi\Parsers\ConfigurationParserInterface;
use AppserverIo\RestApi\Handlers\RequestHandlerInterface;
use AppserverIo\RestApi\Wrappers\OperationWrapperInterface;

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
     * The encoding to mime type mapping.
     *
     * @var array
     */
    protected $formatToMimeType = array(
        FormatKeys::FORMAT_XML  => 'application/xml',
        FormatKeys::FORMAT_JSON => 'application/json'
    );

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
     * Initializes the request handler with the passed instances.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface         $application         The application instance
     * @param \AppserverIo\RestApi\Parsers\RequestParserInterface       $requestParser       The request parser instance
     * @param \AppserverIo\RestApi\Parsers\ConfigurationParserInterface $configurationParser The configuration parser instanc
     */
    public function __construct(
        ApplicationInterface $application,
        RequestParserInterface $requestParser,
        ConfigurationParserInterface $configurationParser
    ) {
        $this->application = $application;
        $this->requestParser = $requestParser;
        $this->configurationParser = $configurationParser;
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
     * Encodes the passed data, to JSON format for example, and returns it.
     *
     * @param object|array $data   The data to be encoded
     * @param string       $format The encoding format (JSON by default)
     *
     * @return string The encoded data
     */
    protected function encode($data, $format = FormatKeys::FORMAT_JSON)
    {
        return SerializerBuilder::create()->build()->serialize($data, $format);
    }

    /**
     * Returns the mime type for the given format, which can either be `xml` or `json`.
     *
     * @param string $format The format to return the mime type for
     *
     * @return string The mime type
     */
    protected function produces($format = FormatKeys::FORMAT_JSON)
    {
        return isset($this->formatToMimeType[$format]) ? $this->formatToMimeType[$format] : FormatKeys::FORMAT_JSON;
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
        foreach ($this->operationWrappers[strtolower($servletRequest->getMethod())] as $oprationWrapper) {
            // query whether or not, the operation's path matches the request path info
            if ($oprationWrapper->match($servletRequest)) {
                try {
                    // load the operations parameters
                    $parameters = $this->getRequestParser()->parse($servletRequest, $oprationWrapper);

                    // lookup the bean instance from the application
                    $instance = $this->getApplication()->search($oprationWrapper->getLookupName());

                    // inovoke the method
                    $result = call_user_func_array(array($instance, $oprationWrapper->getMethodName()), $parameters);

                    // append the result and the headers to the response
                    $servletResponse->addHeader(Protocol::HEADER_CONTENT_TYPE, $this->produces());
                    $servletResponse->appendBodyStream($this->encode($result));
                } catch (\Exception $e) {
                    // log the error
                    \error($e);

                    // send a 500 status code and append the error message to the response
                    $servletResponse->setStatusCode(500);
                    $servletResponse->addHeader(Protocol::HEADER_CONTENT_TYPE, $this->produces());
                    $servletResponse->appendBodyStream($this->encode(array('error' => $e->getMessage())));
                }
            }
        }
    }
}
