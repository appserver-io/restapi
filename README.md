# RESTFul API

[![Latest Stable Version](https://poser.pugx.org/appserver-io/restapi/v/stable.png)](https://packagist.org/packages/appserver-io/restapi)
 [![Total Downloads](https://poser.pugx.org/appserver-io/restapi/downloads.png)](https://packagist.org/packages/appserver-io/restapi)
 [![License](https://poser.pugx.org/appserver-io/restapi/license.png)](https://packagist.org/packages/appserver-io/restapi)
 [![Build Status](https://travis-ci.org/appserver-io/restapi.png)](https://travis-ci.org/appserver-io/restapi)
 [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/appserver-io/restapi/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/appserver-io/restapi/?branch=master)
 [![Code Coverage](https://scrutinizer-ci.com/g/appserver-io/restapi/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/appserver-io/restapi/?branch=master)

## Introduction

RESTFul API provides a simple framework that makes the implemention of a OpenApi 2 (verion 3 is still to come) server pretty simple.

Actually the library only supports a subset of the OpenApi 2 functionality, but we'll add additional during the time.

## Installation

If you want to write an application that uses RESTFul API, you have to install it using Composer. To do this, simply add it to the dependencies in your `composer.json`

```sh
{
    "require": {
        "appserver-io/restapi": "~1.0"
    }
}
```

## Configuration

Simply register the two servlets `AppserverIo\RestApi\Servlets\SwaggerServlet` and `AppserverIo\RestApi\Servlets\ApiServlet` in the `WEB-INF/web.xml` file. The main
description of your webservices can be done through the annotations of Rob Allens library [zircote/swagger-php](https://github.com/zircote/swagger-php/tree/2.x).
 
### The Servlet Configuration

The configuration has to be done in the `WEB-INF/web.xml` file as shown in this example

```xml
<?xml version="1.0" encoding="UTF-8"?>
<web-app xmlns="http://www.appserver.io/appserver">

    <display-name>my-api</display-name>
    <description>My API web application</description>

    <session-config>
        <session-name>my-api/session-name>
        <session-file-prefix>my-api_session_</session-file-prefix>
    </session-config>

    <servlet>
        <description>A servlet that handles DHTML files.</description>
        <display-name>The DHTML Servlet</display-name>
        <servlet-name>dhtml</servlet-name>
        <servlet-class>AppserverIo\Appserver\ServletEngine\Servlets\DhtmlServlet</servlet-class>
    </servlet>

    <servlet>
        <description>A servlet that renders the content of the Swagger definition.</description>
        <display-name>The Swgger Servlet</display-name>
        <servlet-name>swagger</servlet-name>
        <servlet-class>AppserverIo\RestApi\Servlets\SwaggerServlet</servlet-class>
    </servlet>

    <servlet>
        <description>A servlet that handles the RESTFul API requests.</description>
        <display-name>The API Servlet</display-name>
        <servlet-name>api</servlet-name>
        <servlet-class>AppserverIo\RestApi\Servlets\ApiServlet</servlet-class>
        <bean-ref>
            <bean-ref-name>RequestHandlerFactory</bean-ref-name>
            <bean-link>RequestHandlerFactory</bean-link>
            <injection-target>
                <injection-target-class>AppserverIo\RestApi\Servlets\ApiServlet</injection-target-class>
                <injection-target-property>requestHandlerFactory</injection-target-property>
            </injection-target>
        </bean-ref>
        <init-param>
          <param-name>api</param-name>
          <param-value>OA2</param-value>
        </init-param>
    </servlet>

    <servlet-mapping>
        <servlet-name>dhtml</servlet-name>
        <url-pattern>*.dhtml</url-pattern>
    </servlet-mapping>

    <servlet-mapping>
        <servlet-name>swagger</servlet-name>
        <url-pattern>/swagger.do</url-pattern>
    </servlet-mapping>

    <servlet-mapping>
        <servlet-name>api</servlet-name>
        <url-pattern>/api.do</url-pattern>
    </servlet-mapping>

    <!-- ================================================================== -->
    <!-- Error Page Configuration                                           -->
    <!-- ================================================================== -->

    <error-page>
        <error-code-pattern>500</error-code-pattern>
        <error-location>/dhtml/500.dhtml</error-location>
    </error-page>

</web-app>
```

### Annotate the Beans

After the web application has been configured, simple annotate the classes that you want to expose as
webserver with the necessary annotations like

```php
/**
 * A SLSB implementation providing some API functionality.
 *
 * @EPB\Stateless
 *
 * @SWG\Info(
 *     title="My API",
 *     version="1.0.0"
 * )
 *
 * @SWG\Swagger(
 *   schemes={"http"},
 *   host="127.0.0.1:9080",
 *   basePath="/my-api/api.do"
 * )
 */
class SomeProcessor
{

    /**
     * Returns the DTO with the passed ID.
     *
     * @param integer $id The ID of the DTO to return
     *
     * @return \Ma\Api\Dtos\MyDto The DTO with the data
     *
     * @SWG\Get(
     *     path="/traveltaxpackages/{id}",
     *     operationId="find",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the DTO to return",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="The DTO",
     *         @SWG\Schema(
     *             ref="#/definitions/MyDto"
     *         )
     *     )
     * )
     */
    public function find($id)
    {
        // return an array with serializable DTOs here
    }
}
```

## Usage

Open the browser and enter the URL [http://127.0.0.1:9080/my-api/swagger.do](http://127.0.0.1:9080/my-api/swagger.do) which should result in 
rendering the Swagger configuration.
