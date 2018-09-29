<?php

/**
 * AppserverIo\RestApi\Responses\ResponseFactoryInterface
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

use AppserverIo\RestApi\Handlers\RequestHandlerInterface;
use AppserverIo\RestApi\Wrappers\OperationWrapperInterface;

/**
 * Interface for response factory implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 */
interface ResponseFactoryInterface
{

    /**
     * Returns the new response instance.
     *
     * @param \AppserverIo\RestApi\Handlers\RequestHandlerInterface   $requestHandler   The actual request handler instance
     * @param \AppserverIo\RestApi\Wrappers\OperationWrapperInterface $operationWrapper The operation wrapper to create the response for
     * @param \Exception                                              $e                The exception to initialize the resonse with
     *
     * @return \AppserverIo\RestApi\Responses\ResponseInterface The response instance
     */
    public function createResponse(RequestHandlerInterface $requestHandler, OperationWrapperInterface $operationWrapper, \Exception $e);
}
