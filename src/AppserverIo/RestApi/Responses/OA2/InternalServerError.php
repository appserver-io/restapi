<?php

/**
 * AppserverIo\RestApi\Responses\OA2\InternalServerError
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

namespace AppserverIo\RestApi\Responses\OA2;

use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMS;
use AppserverIo\RestApi\Responses\ResponseInterface;

/**
 * OpenApi 2.0 compatible response implementation for HTTP status code 500.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 *
 * @SWG\Definition
 */
class InternalServerError implements ResponseInterface
{

    /**
     * The error message.
     *
     * @var string
     * @SWG\Property
     * @JMS\Type(name="string")
     */
    protected $message;

    /**
     * Sets the passed message.
     *
     * @param string $message The message
     *
     * @return void
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Returns the message.
     *
     * @return string The message
     */
    public function getMessage()
    {
        return $this->message;
    }
}
