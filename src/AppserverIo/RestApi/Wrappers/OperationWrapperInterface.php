<?php

/**
 * AppserverIo\RestApi\Wrappers\OperationWrapperInterface
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

namespace AppserverIo\RestApi\Wrappers;

use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;

/**
 * The interface for all operation wrapper implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 */
interface OperationWrapperInterface
{

    /**
     * Query whether or not the operation matches the passed servlet request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest The HTTP servlet request instance to match
     *
     * @return boolean Returns TRUE, if the operation matches the request, else FALSE
     */
    public function match(HttpServletRequestInterface $servletRequest);

    /**
     * Returns a list of MIME types the operation can produce.
     *
     * @return array The list of MIME types
     */
    public function produces();

    /**
     * Returns the operations parameters.
     *
     * @return \AppserverIo\RestApi\Wrappers\ParameterWrapperInterface[] The array with the parameters
     */
    public function getParameters();

    /**
     * Returns the object lookup name that has to be invoked.
     *
     * @return string The lookup name
     */
    public function getLookupName();

    /**
     * Returns the object method name that has to be invoked.
     *
     * @return string The method name
     */
    public function getMethodName();

    /**
     * Returns an array with the values of the matched path variables.
     *
     * @return array The array with the values
     */
    public function getMatches();

    /**
     * Return's the variable value for the passed parameter.
     *
     * @param \AppserverIo\RestApi\Wrappers\ParameterWrapperInterface $parameter The parameter to return the match for
     *
     * @return mixed The variable value of the passed parameter
     */
    public function getMatch(ParameterWrapperInterface $parameter);

    /**
     * Returns an array with the names of the parsed path variables as key and their position as value.
     *
     * @return array The array with the path variables
     */
    public function getPathVariables();

    /**
     * Returns operation's request method.
     *
     * @return string The request method
     */
    public function getMethod();
}
