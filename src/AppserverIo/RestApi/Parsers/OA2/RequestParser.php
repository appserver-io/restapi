<?php

/**
 * AppserverIo\RestApi\Parsers\OA2\RequestParser
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

namespace AppserverIo\RestApi\Parsers\OA2;

use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\RestApi\Parsers\RequestParserInterface;
use AppserverIo\RestApi\Wrappers\OperationWrapperInterface;
use AppserverIo\RestApi\Wrappers\ParameterWrapperInterface;
use JMS\Serializer\SerializerBuilder;
use AppserverIo\Psr\Di\ObjectManagerInterface;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * OpenApi 2.0 compatible request parser.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 */
class RequestParser implements RequestParserInterface
{

    /**
     * The application instance.
     *
     * @var \AppserverIo\Psr\Application\ApplicationInterface
     */
    protected $application;

    /**
     * Initializes the request handler with the passed application instance.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     */
    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
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
     * Parses the body content, unserializes it and returns it as object instance.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest The HTTP servlet request instance
     * @param \AppserverIo\RestApi\Wrappers\ParameterWrapperInterface   $parameter      The parameter instance
     *
     * @param HttpServletRequestInterface $servletRequest
     * @param ParameterWrapperInterface $parameter
     * @return mixed
     */
    protected function processBodyParameter(HttpServletRequestInterface $servletRequest, ParameterWrapperInterface $parameter)
    {

        // load the object manager instance
        /** @var \AppserverIo\Psr\Di\ObjectManagerInterface $objectManager */
        $objectManager = $this->getApplication()->search(ObjectManagerInterface::IDENTIFIER);

        // extract the lookup name from the ref definition
        list ($lookupName, ) = sscanf($parameter->getSchema()->getRef(), '#/definitions/%s');

        // load the object descriptor
        $objectDescriptor = $objectManager->getObjectDescriptor($lookupName);

        // unserialize the body content into an object and return it
        return SerializerBuilder::create()->build()->deserialize($servletRequest->getBodyContent(), $objectDescriptor->getClassName(), 'json');
    }

    /**
     * Returns the integer value for the passed parameter from the request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest The HTTP servlet request instance
     * @param \AppserverIo\RestApi\Wrappers\ParameterWrapperInterface   $parameter      The parameter instance
     *
     * @return integer|null The parsed value
     * @throws \Exception Is thrown, if the specified collection format invalid
     */
    protected function processArrayParameter(HttpServletRequestInterface $servletRequest, ParameterWrapperInterface $parameter)
    {

        // query the collection format specified for the parameter
        if ($parameter->hasCollectionFormat('csv')) {
            // load the value from the request
            if ($param = $servletRequest->getParameter($parameter->getName())) {
                // initialize the array for the values
                $parameters = array();

                // extract the values by parsing them as CSV string
                foreach (str_getcsv(urldecode($param)) as $value) {
                    list ($key, $val) = explode('=', urldecode($value));
                    $parameters[$key] = $val;
                }

                // return the parameters
                return $parameters;
            }

            // return an empty array
            return array();
        } elseif ($parameter->hasCollectionFormat('multi')) {
            throw new \Exception('Collection format "multi" is not yet supported');
        } else {
            throw new \Exception(sprintf('Unknown collection format "%s" is not supported yet', $parameter->getCollectionFormat()));
        }
    }

    /**
     * Returns the string value for the passed parameter from the request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest The HTTP servlet request instance
     * @param \AppserverIo\RestApi\Wrappers\ParameterWrapperInterface   $parameter      The parameter instance
     *
     * @return string|null The parsed value
     */
    protected function processStringParameter(HttpServletRequestInterface $servletRequest, ParameterWrapperInterface $parameter)
    {
        return $servletRequest->getParameter($parameter->getName());
    }

    /**
     * Returns the integer value for the passed parameter from the request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest The HTTP servlet request instance
     * @param \AppserverIo\RestApi\Wrappers\ParameterWrapperInterface   $parameter      The parameter instance
     *
     * @return integer|null The parsed value
     */
    protected function processIntegerParameter(HttpServletRequestInterface $servletRequest, ParameterWrapperInterface $parameter)
    {
        if ($value = $servletRequest->getParameter($parameter->getName(), FILTER_VALIDATE_INT)) {
            return (integer) $value;
        }
    }

    /**
     * Returns the internal method name to load the value from the request parameters.
     *
     * @param \AppserverIo\RestApi\Wrappers\ParameterWrapperInterface $parameter The parameter to create prepare the method name for
     *
     * @return string The prepared method name
     */
    protected function prepareMethodName(ParameterWrapperInterface $parameter)
    {
        return sprintf('process%sParameter', ucfirst($parameter->getType()));
    }

    /**
     * Parses the HTTP request for request parameters defined by the also passed operation.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest   The HTTP servlet request instance
     * @param \AppserverIo\RestApi\Wrappers\OperationWrapperInterface   $operationWrapper The operation wrapper
     *
     * @return array The array with the operations values pasrsed from the request
     * @throws \Exception is thrown, the context of the operation's parameter is invalid
     */
    public function parse(HttpServletRequestInterface $servletRequest, OperationWrapperInterface $operationWrapper)
    {

        // initialize the array for the parsed values
        $parameters = array();

        // iterate over the operations parameters and try load the value from the request
        foreach ($operationWrapper->getParameters() as $parameter) {
            // query the parameter context
            if ($parameter->isIn('path')) {
                // extract the value from the path info
                $value = $operationWrapper->getMatch($parameter);

                // cast the value depending on the specified type
                if ($parameter->getType() === 'integer') {
                    $parameters[] = (integer) $value;
                } elseif ($parameter->getType() === 'float') {
                    $parameters[] = (float) $value;
                } else {
                    $parameters[] = $value;
                }
            } elseif ($parameter->isIn('query')) {
                // try to load the value from the request parameters
                $parameters[] = call_user_func(array($this, $this->prepareMethodName($parameter)), $servletRequest, $parameter);
            } elseif ($parameter->isIn('header')) {
                // TODO Still to implement
            } elseif ($parameter->isIn('body')) {
                // try to load the value from the request parameters
                $parameters[] = $this->processBodyParameter($servletRequest, $parameter);
            } elseif ($parameter->isIn('formData')) {
                // TODO Still to implement
            } else {
                throw new \Exception(sprintf('Query parameter context "%s" is not supported yed', $parameter->getIn()));
            }
        }

        // return the array with parsed values
        return $parameters;
    }
}
