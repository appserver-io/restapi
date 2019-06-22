<?php

/**
 * AppserverIo\RestApi\Wrappers\OA2\HeaderWrapper
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

use Swagger\Annotations\Header;
use AppserverIo\RestApi\Wrappers\HeaderWrapperInterface;

/**
 * OpenApi 2.0 compatible header wrapper.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 */
class HeaderWrapper implements HeaderWrapperInterface
{

    /**
     * The wrapped header instance.
     *
     * @var \Swagger\Annotations\Header
     */
    protected $header;

    /**
     * Initializes the wrapper with the passed instances.
     *
     * @param \Swagger\Annotations\Header $header The header that has to be wrapped
     */
    public function __construct(Header $header)
    {
        $this->header = $header;
    }

    /**
     * Returns the wrapped header instance.
     *
     * @return \Swagger\Annotations\Header The header instance
     */
    protected function getHeader()
    {
        return $this->header;
    }
}
