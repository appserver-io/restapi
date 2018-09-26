<?php

/**
 * AppserverIo\RestApi\Servlets\ApiServlet
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

namespace AppserverIo\RestApi\Servlets;

use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\ServletConfigInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
use AppserverIo\RestApi\Handlers\RequestHandlerInterface;
use AppserverIo\RestApi\Utils\InitParameterKeys;

/**
 * Controller servlet handling any API requests.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 */
class ApiServlet extends HttpServlet
{

    /**
     * The request handler factory instance to use.
     *
     * @var \AppserverIo\RestApi\Handlers\RequestHandlerFactory
     */
    protected $requestHandlerFactory;

    /**
     * The request handler instance to use.
     *
     * @var \AppserverIo\RestApi\Handlers\RequestHandlerInterface
     */
    protected $requestHandler;

    /**
     * Sets the request handler instance to use.
     *
     * @param \AppserverIo\RestApi\Handlers\RequestHandlerInterface $requestHandler The request handler instance to use
     *
     * @return void
     */
    protected function setRequestHandler(RequestHandlerInterface $requestHandler)
    {
        $this->requestHandler = $requestHandler;
    }

    /**
     * Returns the request handler instance.
     *
     * @return \AppserverIo\RestApi\Handlers\RequestHandlerInterface The parser instance
     */
    protected function getRequestHandler()
    {
        return $this->requestHandler;
    }

    /**
     * Returns the request handler factory instance.
     *
     * @return \AppserverIo\RestApi\Handlers\RequestHandlerFactory The request handler factory instance
     */
    protected function getRequestHandlerFactory()
    {
        return $this->requestHandlerFactory;
    }

    /**
     * Initializes the servlet with the passed configuration.
     *
     * @param \AppserverIo\Psr\Servlet\ServletConfigInterface $servletConfig The configuration to initialize the servlet with
     *
     * @throws \AppserverIo\Psr\Servlet\ServletException Is thrown if the configuration has errors
     * @return void
     * @see \AppserverIo\Psr\Servlet\GenericServlet::init()
     */
    public function init(ServletConfigInterface $servletConfig)
    {

        // call parent method
        parent::init($servletConfig);

        // create and initialize a new request handler instance
        $this->setRequestHandler(
            $this->getRequestHandlerFactory()->createRequestHandler(
                $servletConfig->getInitParameter(InitParameterKeys::API)
            )
        );
    }

    /**
     * Handles a HTTP GET request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
     *
     * @return void
     * @throws \AppserverIo\Psr\Servlet\ServletException Is thrown if the request method is not implemented
     * @see \AppserverIo\Psr\Servlet\Http\HttpServlet::doGet()
     */
    public function doGet(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {
        $this->getRequestHandler()->handle($servletRequest, $servletResponse);
    }

    /**
     * Implements Http POST method.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
     *
     * @return void
     * @throws \AppserverIo\Psr\Servlet\ServletException Is thrown if the request method is not implemented
     */
    public function doPost(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {
        $this->getRequestHandler()->handle($servletRequest, $servletResponse);
    }

    /**
     * Implements Http PATCH method.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
     *
     * @return void
     * @throws \AppserverIo\Psr\Servlet\ServletException Is thrown if the request method is not implemented
     */
    public function doPatch(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {
        $this->getRequestHandler()->handle($servletRequest, $servletResponse);
    }

    /**
     * Implements Http DELETE method.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
     *
     * @return void
     * @throws \AppserverIo\Psr\Servlet\ServletException Is thrown if the request method is not implemented
     */
    public function doDelete(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {
        $this->getRequestHandler()->handle($servletRequest, $servletResponse);
    }
}
