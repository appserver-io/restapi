<?php

/**
 * AppserverIo\RestApi\Wrappers\OA2\OperationWrapper
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

namespace AppserverIo\RestApi\Wrappers\OA2;

use Swagger\Annotations\Operation;
use AppserverIo\RestApi\Wrappers\ParameterWrapperInterface;
use AppserverIo\RestApi\Wrappers\OperationWrapperInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\EnterpriseBeans\Description\NameAwareDescriptorInterface;

/**
 * OpenApi 2.0 compatible operation wrapper.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 */
class OperationWrapper implements OperationWrapperInterface
{

    /**
     * The operation to wrap.
     *
     * @var \Swagger\Annotations\Operation
     */
    protected $operation;

    /**
     * The lookup name of the object the operation has to be invoked on.
     *
     * @var string
     */
    protected $lookupName;

    /**
     * The method name of the object that has to be invoked.
     *
     * @var string
     */
    protected $methodName;

    /**
     * The regular expression to match the operation's path to the request's path info.
     *
     * @var string
     */
    protected $pattern;

    /**
     * The operation's wrapped parameters.
     *
     * @var \AppserverIo\RestApi\Wrappers\ParameterWrapperInterface[]
     */
    protected $parameters = array();

    /**
     * The operation's wrapped responses.
     *
     * @var array
     */
    protected $responses = array();

    /**
     * The array with the names of the parsed path variables as key and their position as value.
     *
     * @var array
     */
    protected $pathVariables = array();

    /**
     * The array with the values of the matched path variables.
     *
     * @var array
     */
    protected $matches = array();

    /**
     * Initializes the wrapper with the passed instances.
     *
     * @param \Swagger\Annotations\Operation                                            $operation        The operation that has to be wrapped
     * @param \AppserverIo\Psr\EnterpriseBeans\Description\NameAwareDescriptorInterface $objectDescriptor The object descriptor of the object the operation has to be invoked on
     * @param \ReflectionMethod                                                         $reflectionMethod The reflection method with the method name of the object that has to be invoked
     */
    public function __construct(Operation $operation, NameAwareDescriptorInterface $objectDescriptor, \ReflectionMethod $reflectionMethod)
    {

        // set the passed instances
        $this->operation = $operation;
        $this->lookupName = $objectDescriptor->getName();
        $this->methodName = $reflectionMethod->getName();

        // wrap the operation's parameters
        if (is_array($operation->parameters)) {
            foreach ($operation->parameters as $parameter) {
                $this->parameters[] = new ParameterWrapper($parameter);
            }
        }

        // wrap the operation's responses
        if (is_array($operation->responses)) {
            foreach ($operation->responses as $response) {
                $this->responses[$response->response] = new ResponseWrapper($response);
            }
        }

        // initialize the array for the path variables
        $pathVariables = array();

        // extract the path variables from the operations path
        preg_match('/\\\{[a-zA-Z0-9\_\-]+\\\}/', preg_quote($operation->path), $pathVariables);

        // remove the surrounding braces
        array_walk($pathVariables, function (&$pathVariable) {
            $pathVariable = stripslashes(str_replace(array('{', '}'), null, $pathVariable));
        });

        // set the found path variables
        $this->pathVariables = array_flip($pathVariables);

        // initialize the regex to match the path info to the operation's path
        $this->pattern = "@^" . preg_replace('/\\\{[a-zA-Z0-9\_\-]+\\\}/', '([a-zA-Z0-9\-\_]+)', preg_quote($operation->path)) . "$@D";
    }

    /**
     * Returns the operation instance.
     *
     * @return \Swagger\Annotations\Operation The operation instance
     */
    protected function getOperation()
    {
        return $this->operation;
    }

    /**
     * Query whether or not the operation matches the passed servlet request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest The HTTP servlet request instance to match
     *
     * @return boolean Returns TRUE, if the operation matches the request, else FALSE
     */
    public function match(HttpServletRequestInterface $servletRequest)
    {

        // try to match the request instance
        $match = preg_match($this->pattern, $servletRequest->getPathInfo(), $this->matches);

        // remove the first match, because it's the path info
        array_shift($this->matches);

        // return TRUE, if the request matches, else FALSE
        return $match;
    }

    /**
     * Returns a list of MIME types the operation can produce.
     *
     * @return array The list of MIME types
     */
    public function produces()
    {
        return $this->getOperation()->produces;
    }

    /**
     * Returns the operation's parameters.
     *
     * @return \AppserverIo\RestApi\Wrappers\ParameterWrapperInterface[] The array with the parameters
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Returns the object lookup name that has to be invoked.
     *
     * @return string The lookup name
     */
    public function getLookupName()
    {
        return $this->lookupName;
    }

    /**
     * Returns the object method name that has to be invoked.
     *
     * @return string The method name
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * Returns an array with the values of the matched path variables.
     *
     * @return array The array with the values
     */
    public function getMatches()
    {
        return $this->matches;
    }

    /**
     * Return's the variable value for the passed parameter.
     *
     * @param \AppserverIo\RestApi\Wrappers\ParameterWrapperInterface $parameter The parameter to return the match for
     *
     * @return mixed The variable value of the passed parameter
     */
    public function getMatch(ParameterWrapperInterface $parameter)
    {
        return $this->matches[$this->pathVariables[$parameter->getName()]];
    }

    /**
     * Returns an array with the names of the parsed path variables as key and their position as value.
     *
     * @return array The array with the path variables
     */
    public function getPathVariables()
    {
        return $this->pathVariables;
    }

    /**
     * Returns operation's request method.
     *
     * @return string The request method
     */
    public function getMethod()
    {
        return $this->getOperation()->method;
    }

    /**
     * Return's the response for the passed status code.
     *
     * @param mixed $statusCode The status code to return the response for
     *
     * @return \AppserverIo\RestApi\Wrappers\ResponseWrapperInterface The repsonse wrapper instance
     * @throws \Exception Is thrown, if the response for the passed status code is not available
     */
    public function getResponse($statusCode)
    {

        // query whether or not a response for the passed status code is available
        if (isset($this->responses[$statusCode])) {
            return $this->responses[$statusCode];
        }

        // throw an exception, if not
        throw new \Exception(sprintf('Can\'t find response for status code "%s"', $statusCode));
    }

    /**
     * Returns the operation's responses.
     *
     * @return \AppserverIo\RestApi\Wrappers\ResponseWrapperInterface[] The array with the responses
     */
    public function getResponses()
    {
        return $this->responses;
    }
}
