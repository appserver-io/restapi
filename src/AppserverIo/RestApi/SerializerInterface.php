<?php

/**
 * AppserverIo\RestApi\SerializerInterface
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

namespace AppserverIo\RestApi;

use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;

/**
 * Interface for serializer implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 */
interface SerializerInterface
{

    /**
     * Mapps the Accept header of the passed HTTP servlet request to the type supported by the serializer.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest The HTTP servlet request instance
     *
     * @return string The serializer type to use
     */
    public function mapHeader(HttpServletRequestInterface $servletRequest);

    /**
     * Serializes the passed data with the given format.
     *
     * @param mixed  $data   The data to serialize
     * @param string $format The format used for serialization
     *
     * @return mixed The serialized data
     */
    public function serialize($data, $format = 'json');

    /**
     * Unserialize the passed data into an object and return it.
     *
     * @param string $data   The data to unserialize
     * @param string $type   The type to convert the unserialized data to
     * @param string $format The format, the data has been serialized to
     *
     * @return mixed The unserialized data
     */
    public function unserialize($data, $type, $format = 'json');
}
