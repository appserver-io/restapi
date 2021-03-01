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

use AppserverIo\Psr\Di\ObjectManagerInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\RestApi\Parsers\RequestParserInterface;
use AppserverIo\RestApi\Wrappers\OperationWrapperInterface;
use AppserverIo\RestApi\Wrappers\ParameterWrapperInterface;
use AppserverIo\Psr\EnterpriseBeans\Description\BeanDescriptorInterface;
use AppserverIo\RestApi\SerializerInterface;

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
     * The serializer instance.
     *
     * @var \AppserverIo\RestApi\SerializerInterface
     */
    protected $serializer;

    /**
     * Initializes the request handler with the passed application instance.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     * @param \AppserverIo\RestApi\SerializerInterface          $serializer  The serializer instance
     */
    public function __construct(ApplicationInterface $application, SerializerInterface $serializer)
    {
        $this->application = $application;
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
     * Returns the serializer instance.
     *
     * @return \AppserverIo\RestApi\SerializerInterface The serializer instance
     */
    protected function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * Unserializes the body content of the passed request and converts the data into an
     * object specified by the passed bean descriptor instance.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface            $servletRequest The HTTP servlet request instance
     * @param \AppserverIo\Psr\EnterpriseBeans\Description\BeanDescriptorInterface $descriptor     The bean descriptor with the class definition
     *
     * @return mixed The unserialized body content
     */
    protected function unserialize(HttpServletRequestInterface $servletRequest, BeanDescriptorInterface $descriptor)
    {
        return $this->getSerializer()->unserialize($servletRequest->getBodyContent(), $descriptor->getClassName(), $this->getSerializer()->mapHeader($servletRequest));
    }

    /**
     * Parses the body content, unserializes it and returns it as object instance.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest The HTTP servlet request instance
     * @param \AppserverIo\RestApi\Wrappers\ParameterWrapperInterface   $parameter      The parameter instance
     *
     * @return object The parameter converted into an object
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

        // unserialize the body content and return the bean instance
        return $this->unserialize($servletRequest, $objectDescriptor);
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
                // extract the values by parsing them as CSV string
                return str_getcsv($param);
            }

            // return an empty array
            return array();
        } elseif ($parameter->hasCollectionFormat('multi')) {
            // load the value from the request
            if ($param = $servletRequest->getParam($parameter->getName())) {
                // initialize the array for the values
                $parameters = array();

                // extract the values by parsing them as CSV string
                foreach ($param as $key => $value) {
                    $parameters[$key] = filter_var($value, FILTER_SANITIZE_STRING);
                }

                // return the parameters
                return $parameters;
            }
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
