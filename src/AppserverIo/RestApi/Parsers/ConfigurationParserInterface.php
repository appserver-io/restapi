<?php

/**
 * AppserverIo\RestApi\Parsers\AnnotationParserInterface
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

namespace AppserverIo\RestApi\Parsers;

use AppserverIo\RestApi\Handlers\RequestHandlerInterface;

/**
 * Interface for a configuration parser instance.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 */
interface ConfigurationParserInterface
{

    /**
     * Parses the OpenApi annotations of the application's beans and adds the
     * necessery operations to the passed servlet.
     *
     * @param \AppserverIo\RestApi\Handlers\RequestHandlerInterface $requestHandler The request handler instance
     *
     * @return void
     */
    public function parse(RequestHandlerInterface $requestHandler);
}
