<?php

/**
 * AppserverIo\RestApi\Handlers\RequestHandlerInterface
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

namespace AppserverIo\RestApi\Handlers;

use AppserverIo\RestApi\Wrappers\OperationWrapperInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

/**
 * The interface for RESTFul API request handlers.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 */
interface RequestHandlerInterface
{

    /**
     * Initializes the request handler.
     *
     * @return void
     */
    public function init();

    /**
     * Adds the passed operation wrapper to the request handler.
     *
     * @param \AppserverIo\RestApi\Wrappers\OperationWrapperInterface $operationWrapper The operation wrapper to add
     *
     * @return void
     */
    public function addOperation(OperationWrapperInterface $operationWrapper);

    /**
     * Process the passed request instance.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The HTTP servlet request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The HTTP servlet response instance
     *
     * @return void
     */
    public function handle(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse);
}
