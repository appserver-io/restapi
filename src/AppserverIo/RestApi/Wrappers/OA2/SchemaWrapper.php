<?php

/**
 * AppserverIo\RestApi\Wrappers\OA2\SchemaWrapper
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

use Swagger\Annotations\Schema;
use AppserverIo\RestApi\Wrappers\SchemaWrapperInterface;

/**
 * The wrapper implementation for a schema wrapper.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 */
class SchemaWrapper implements SchemaWrapperInterface
{

    /**
     * The parameter to be wrapped.
     *
     * @var \Swagger\Annotations\Schema
     */
    protected $schema;

    /**
     * Initializes the instance with the schema that has to be wrapped.
     *
     * @param \Swagger\Annotations\Schema $schema The schema instance
     */
    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
    }

    /**
     * Returns the wrapped schema instance.
     *
     * @return \Swagger\Annotations\Schema The schema instance
     */
    protected function getSchema()
    {
        return $this->schema;
    }

    /**
     * Returns the schema definition.
     *
     * @return string The schema definition
     * @see http://json-schema.org/latest/json-schema-core.html#rfc.section.7.
     */
    public function getRef()
    {
        return $this->getSchema()->ref;
    }

    /**
     * Returns the schema's collection format.
     *
     * @return string The collection format
     */
    public function getCollectionFormat()
    {
        return $this->getSchema()->collectionFormat;
    }

    /**
     * Query whether or not the schema has the passed collection format.
     *
     * @param string $collectionFormat The collection format to query
     *
     * @return boolean TRUE if collection format matches, else FALSE
     */
    public function hasCollectionFormat($collectionFormat)
    {
        return strcasecmp($this->getCollectionFormat(), $collectionFormat) === 0 ? true : false;
    }
}
