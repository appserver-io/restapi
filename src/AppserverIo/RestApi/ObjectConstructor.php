<?php

/**
 * AppserverIo\RestApi\ObjectConstructor
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

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\VisitorInterface;
use JMS\Serializer\Construction\ObjectConstructorInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Lang\Reflection\ReflectionClass;

/**
 * Implementations of this interface construct new objects during deserialization.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 */
class ObjectConstructor implements ObjectConstructorInterface
{

    /**
     * The application instance.
     *
     * @var \AppserverIo\Psr\Application\ApplicationInterface
     */
    protected $application;

    /**
     * Initializes the object constructor instance with the application
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     */
    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * Constructs a new object.
     *
     * Implementations could for example create a new object calling "new", use
     * "unserialize" techniques, reflection, or other means.
     *
     * @param \JMS\Serializer\VisitorInterface       $visitor  The visitor instance
     * @param \JMS\Serializer\Metadata\ClassMetadata $metadata The class metadata
     * @param mixed                                  $data     The data passed to the constructor
     * @param array                                  $type     The array with type information ["name" => string, "params" => array]
     * @param \JMS\Serializer\DeserializationContext $context  The deserialization context
     *
     * @return object The instance
     */
    public function construct(VisitorInterface $visitor, ClassMetadata $metadata, $data, array $type, DeserializationContext $context)
    {

        // create a reflection class implementation to load the annotations
        $reflectionClass = new ReflectionClass($metadata->name);

        // try to lookup the instance in the container
        return $this->application->search($reflectionClass->getShortName());
    }
}
