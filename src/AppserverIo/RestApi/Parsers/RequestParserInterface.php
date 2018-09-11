<?php

/**
 * AppserverIo\RestApi\Parsers\RequestParserInterface
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
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 */

namespace AppserverIo\RestApi\Parsers;

use AppserverIo\RestApi\Wrappers\OperationWrapperInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;

/**
 * The interface for all request parser implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 */
interface RequestParserInterface
{

    /**
     * Parses the HTTP request for request parameters defined by the also passed operation.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest   The HTTP servlet request instance
     * @param \AppserverIo\RestApi\Wrappers\OperationWrapperInterface   $operationWrapper The operation wrapper
     *
     * @return array The array with the operations values passed from the request
     */
    public function parse(HttpServletRequestInterface $servletRequest, OperationWrapperInterface $operationWrapper);
}
