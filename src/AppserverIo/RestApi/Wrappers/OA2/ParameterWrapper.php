<?php

/**
 * AppserverIo\RestApi\Wrappers\OA2\OperationWrapper
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

use Swagger\Annotations\Parameter;
use AppserverIo\RestApi\Wrappers\ParameterWrapperInterface;

/**
 * The wrapper implementation for a parameter wrapper.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 */
class ParameterWrapper implements ParameterWrapperInterface
{

    /**
     * The parameter to be wrapped.
     *
     * @var \Swagger\Annotations\Parameter
     */
    protected $parameter;

    /**
     * Initializes the instance with the parameter that has to be wrapped.
     *
     * @param \Swagger\Annotations\Parameter $parameter The parameter
     */
    public function __construct(Parameter $parameter)
    {
        $this->parameter = $parameter;
    }

    /**
     * Returns the wrapped parameter instance.
     *
     * @return \Swagger\Annotations\Parameter
     */
    protected function getParameter()
    {
        return $this->parameter;
    }

    /**
     * Query whether or not the parameter is in the passed scope.
     *
     * @param string $in The scope to query
     *
     * @return boolean TRUE if the scope matches, else FALSE
     */
    public function isIn($in)
    {
        return strcasecmp($this->getIn(), $in) === 0 ? true : false;
    }

    /**
     * Returns the location of the parameter.
     *
     * @return string The location of the parameter
     */
    public function getIn()
    {
        return $this->getParameter()->in;
    }

    /**
     * Returns the type of the parameter.
     *
     * @return string The parameter type
     */
    public function getType()
    {
        return $this->getParameter()->type;
    }

    /**
     * Returns the parameter's name.
     *
     * @return string The name
     */
    public function getName()
    {
        return $this->getParameter()->name;
    }

    /**
     * Returns the parameter's collection format.
     *
     * @return string The collection format
     */
    public function getCollectionFormat()
    {
        return $this->getParameter()->collectionFormat;
    }

    /**
     * Query whether or not the parameter has the passed collection format.
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
