<?php

/**
 * AppserverIo\RestApi\Wrappers\ResponseWrapperInterface
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

/**
 * The interface for all response wrapper implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 */
interface ResponseWrapperInterface
{

    /**
     * Returns the schema definition.
     *
     * @return string The schema definition
     * @see http://json-schema.org/latest/json-schema-core.html#rfc.section.7.
     */
    public function getRef();

    /**
     * Returns the HTTP status code for the response.
     *
     * @return string The HTTP status code
     */
    public function getStatusCode();

    /**
     * Returns the response description.
     *
     * @return string The description
     */
    public function getDescription();

    /**
     * Returns the response's schema instance.
     *
     * @return \AppserverIo\RestApi\Wrappers\SchemaWrapperInterface The schema instance
     */
    public function getSchema();

    /**
     * Returns a list of headers that are sent with the response.
     *
     * @return \AppserverIo\RestApi\Wrappers\HeaderWrapperInterface[] The list with headers
     */
    public function getHeaders();
}
