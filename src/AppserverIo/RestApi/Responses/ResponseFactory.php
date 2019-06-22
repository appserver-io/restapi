<?php

/**
 * AppserverIo\RestApi\Responses\ResponseFactory
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

namespace AppserverIo\RestApi\Responses;

use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\RestApi\Handlers\RequestHandlerInterface;
use AppserverIo\RestApi\Wrappers\OperationWrapperInterface;

/**
 * Generic response factory implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 */
class ResponseFactory implements ResponseFactoryInterface
{

    /**
     * The application instance.
     *
     * @var \AppserverIo\Psr\Application\ApplicationInterface
     */
    protected $application;

    /**
     * Initializes the factory with the application instance.
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
     * Returns the new response instance.
     *
     * @param \AppserverIo\RestApi\Handlers\RequestHandlerInterface   $requestHandler   The actual request handler instance
     * @param \AppserverIo\RestApi\Wrappers\OperationWrapperInterface $operationWrapper The operation wrapper to create the response for
     * @param \Exception                                              $e                The exception to initialize the resonse with
     *
     * @return \AppserverIo\RestApi\Responses\ResponseInterface The response instance
     */
    public function createResponse(RequestHandlerInterface $requestHandler, OperationWrapperInterface $operationWrapper, \Exception $e)
    {

        // try to load the response wrapper by the exception's status code
        $responseWrapper = $operationWrapper->getResponse($e->getCode() ? $e->getCode() : 500);

        // extract the lookup name from the ref definition
        list ($lookupName, ) = sscanf($responseWrapper->getSchema()->getRef(), '#/definitions/%s');

        // load the response instance for the API that matches the passed request handler
        $response = $this->getApplication()->search(sprintf('%s/%s', $requestHandler->getApi(), $lookupName));

        // add the exception message
        $response->setMessage($e->getMessage());

        // return the response instance
        return $response;
    }
}
