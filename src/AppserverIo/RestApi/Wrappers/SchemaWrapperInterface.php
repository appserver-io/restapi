<?php

/**
 * AppserverIo\RestApi\Wrappers\SchemaWrapperInterface
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
 * The interface for all schema wrapper implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 */
interface SchemaWrapperInterface
{

    /**
     * Returns the schema definition.
     *
     * @return string The schema definition
     * @see http://json-schema.org/latest/json-schema-core.html#rfc.section.7.
     */
    public function getRef();

    /**
     * Returns the schema's collection format.
     *
     * @return string The collection format
     */
    public function getCollectionFormat();

    /**
     * Query whether or not the schema has the passed collection format.
     *
     * @param string $collectionFormat The collection format to query
     *
     * @return boolean TRUE if collection format matches, else FALSE
     */
    public function hasCollectionFormat($collectionFormat);
}
