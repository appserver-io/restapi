<?php

/**
 * AppserverIo\RestApi\Servlets\AnnotatedServlet
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

use Symfony\Component\Finder\Finder;
use AppserverIo\RestApi\Utils\InitParameterKeys;
use AppserverIo\Psr\HttpMessage\Protocol;
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\ServletConfigInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

/**
 * Annotated servlet that renders an OpenApi 2.0 compatible swagger JSON configuration file.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 */
class SwaggerServlet extends HttpServlet
{

    /**
     * The content of the swagger JSON file.
     *
     * @var string
     */
    protected $swagger;

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

        // initialize the finder for a fine granular
        $finder = new Finder();
        $finder->files('*.php');
        $finder->in(
            array(
                sprintf('%s/Responses/%s', dirname(__DIR__), $servletConfig->getInitParameter(InitParameterKeys::API)),
                sprintf('%s/*/classes', $servletConfig->getWebappPath())
            )
        );

        // generate the content of the Swagger JSON file
        $this->swagger = \Swagger\scan($finder);
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
        $servletResponse->addHeader(Protocol::HEADER_CONTENT_TYPE, 'application/json');
        $servletResponse->appendBodyStream($this->swagger);
    }
}
