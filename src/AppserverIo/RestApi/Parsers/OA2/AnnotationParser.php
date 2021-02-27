<?php

/**
 * AppserverIo\RestApi\Parsers\OA2\AnnotationParser
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

namespace AppserverIo\RestApi\Parsers\OA2;

use Swagger\Annotations\Get;
use Swagger\Annotations\Post;
use Swagger\Annotations\Patch;
use Swagger\Annotations\Delete;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationException;
use AppserverIo\RestApi\Wrappers\OA2\OperationWrapper;
use AppserverIo\RestApi\Handlers\RequestHandlerInterface;
use AppserverIo\RestApi\Parsers\ConfigurationParserInterface;
use AppserverIo\Psr\Di\ObjectManagerInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\EnterpriseBeans\Description\NameAwareDescriptorInterface;

/**
 * OpenApi 2.0 compatible annotation parser.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/restapi
 * @link      http://www.appserver.io
 */
class AnnotationParser implements ConfigurationParserInterface
{

    /**
     * The application instance.
     *
     * @var \AppserverIo\Psr\Application\ApplicationInterface
     */
    protected $application;

    /**
     * Returns the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     */
    protected function getApplication()
    {
        return $this->application;
    }

    /**
     * Initializes the request handler with the passed instances.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     */
    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * Parses the OpenApi annotations of the application's beans and adds the
     * found operations to the passed request handler.
     *
     * @param \AppserverIo\RestApi\Handlers\RequestHandlerInterface $requestHandler The request handler instance
     *
     * @return void
     */
    public function parse(RequestHandlerInterface $requestHandler)
    {

        // initialize the annotation reader instance
        $annotationReader = new AnnotationReader();

        // load the object manager from the application
        /** @var \AppserverIo\Psr\Di\ObjectManagerInterface $objectManager */
        $objectManager = $this->getApplication()->search(ObjectManagerInterface::IDENTIFIER);

        // iterate over all object descriptors and parse their methods for OpenApi annotations
        foreach ($objectManager->getObjectDescriptors() as $objectDescriptor) {
            // create a reflection class of the object descriptor's class name
            $reflectionClass = new \ReflectionClass($objectDescriptor->getClassName());

            // query wheter or not we've name aware object descriptor
            if ($objectDescriptor instanceof NameAwareDescriptorInterface) {
                // if yes, iterate over the methods
                foreach ($reflectionClass->getMethods() as $reflectionMethod) {
                    try {
                        // adn add the GET operation, if the method has the apropriate annotation
                        if ($operation = $annotationReader->getMethodAnnotation($reflectionMethod, Get::class)) {
                            $requestHandler->addOperation(new OperationWrapper($operation, $objectDescriptor, $reflectionMethod));
                        }

                        // adn add the POST operation, if the method has the apropriate annotation
                        if ($operation = $annotationReader->getMethodAnnotation($reflectionMethod, Post::class)) {
                            $requestHandler->addOperation(new OperationWrapper($operation, $objectDescriptor, $reflectionMethod));
                        }

                        // adn add the PATCH operation, if the method has the apropriate annotation
                        if ($operation = $annotationReader->getMethodAnnotation($reflectionMethod, Patch::class)) {
                            $requestHandler->addOperation(new OperationWrapper($operation, $objectDescriptor, $reflectionMethod));
                        }

                        // adn add the DELETE operation, if the method has the apropriate annotation
                        if ($operation = $annotationReader->getMethodAnnotation($reflectionMethod, Delete::class)) {
                            $requestHandler->addOperation(new OperationWrapper($operation, $objectDescriptor, $reflectionMethod));
                        }
                    } catch(AnnotationException $ae) {
                        \warning($ae->getMessage());
                    }
                }
            }
        }
    }
}
