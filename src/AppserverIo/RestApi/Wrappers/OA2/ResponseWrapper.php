<?php

/**
 * AppserverIo\RestApi\Wrappers\OA2\ResponseWrapper
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

use Swagger\Annotations\Response;
use AppserverIo\RestApi\Wrappers\ResponseWrapperInterface;

/**
 * OpenApi 2.0 compatible response wrapper.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 */
class ResponseWrapper implements ResponseWrapperInterface
{

    /**
     * The wrapped response instance.
     *
     * @var \Swagger\Annotations\Response
     */
    protected $response;

    /**
     * A list of headers that are sent with the response.
     *
     * @var \AppserverIo\RestApi\Wrappers\HeaderWrapperInterface[]
     */
    protected $headers = array();

    /**
     * Initializes the wrapper with the passed instances.
     *
     * @param \Swagger\Annotations\Response $response The response that has to be wrapped
     */
    public function __construct(Response $response)
    {

        // set the passed instances
        $this->response = $response;

        // wrap the response's headers
        if (is_array($response->headers)) {
            foreach ($response->headers as $header) {
                $this->headers = new HeaderWrapper($header);
            }
        }
    }

    /**
     * Returns the wrapped response instance.
     *
     * @return \Swagger\Annotations\Response The response instance
     */
    protected function getResponse()
    {
        return $this->response;
    }

    /**
     * Returns the schema definition.
     *
     * @return string The schema definition
     * @see http://json-schema.org/latest/json-schema-core.html#rfc.section.7.
     */
    public function getRef()
    {
        return $this->getResponse()->ref;
    }

    /**
     * Returns the HTTP status code for the response.
     *
     * @return string The HTTP status code
     */
    public function getStatusCode()
    {
        $this->getResponse()->response;
    }

    /**
     * Returns the response description.
     *
     * @return string The description
     */
    public function getDescription()
    {
        return $this->getResponse()->description;
    }

    /**
     * Returns the response's schema instance.
     *
     * @return \AppserverIo\RestApi\Wrappers\SchemaWrapperInterface The schema instance
     */
    public function getSchema()
    {
        return new SchemaWrapper($this->getResponse()->schema);
    }

    /**
     * Returns a list of headers that are sent with the response.
     *
     * @return \AppserverIo\RestApi\Wrappers\HeaderWrapperInterface[] The list with headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
