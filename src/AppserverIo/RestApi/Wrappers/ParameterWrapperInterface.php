<?php

/**
 * AppserverIo\RestApi\Wrappers\ParameterWrapperInterface
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
 * The interface for all parameter wrapper implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 */
interface ParameterWrapperInterface
{

    /**
     * Query whether or not the parameter is in the passed scope.
     *
     * @param string $in The scope to query
     *
     * @return boolean TRUE if the scope matches, else FALSE
     */
    public function isIn($in);

    /**
     * Returns the location of the parameter.
     *
     * @return string The location of the parameter
     */
    public function getIn();

    /**
     * Returns the type of the parameter.
     *
     * @return string The parameter type
     */
    public function getType();

    /**
     * Returns the parameter's name.
     *
     * @return string The name
     */
    public function getName();

    /**
     * Returns the parameter's collection format.
     *
     * @return string The collection format
     */
    public function getCollectionFormat();

    /**
     * Query whether or not the parameter has the passed collection format.
     *
     * @param string $collectionFormat The collection format to query
     *
     * @return boolean TRUE if collection format matches, else FALSE
     */
    public function hasCollectionFormat($collectionFormat);
}
