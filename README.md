# Apigee Edge PHP SDK

**Currently the 2.x version is under active development. Use it on your own risk or install the latest 1.x version!**

[![Build Status](https://travis-ci.org/apigee/edge-php-sdk.svg?branch=2.x-dev)](https://travis-ci.org/apigee/edge-php-sdk)
[![Code Coverage](https://codecov.io/gh/apigee/edge-php-sdk/branch/2.x-dev/graph/badge.svg)](https://codecov.io/gh/apigee/edge-php-sdk/branch/2.x-dev)
[![Latest Stable Version](https://poser.pugx.org/apigee/edge/v/stable)](https://packagist.org/packages/apigee/edge)
[![Total Downloads](https://poser.pugx.org/apigee/edge/downloads)](https://packagist.org/packages/apigee/edge)
[![Latest Unstable Version](https://img.shields.io/badge/unstable-2.x--dev-orange.svg?style=flat-square)](https://packagist.org/packages/apigee/edge)
[![Minimum PHP Version](https://img.shields.io/badge/PHP-%3E%3D%207.1-8892BF.svg?style=flat-square)](https://php.net/)
[![License](https://poser.pugx.org/apigee/edge/license)](https://packagist.org/packages/apigee/edge)

Getting started
---------- 

The Apigee PHP Edge SDK is an HTTP client implementation-independent library thanks for the [HTTPlug](http://docs.php-http.org/en/latest/)
library. It means that you, as a developer, can choose the client that best fits your project and use the same
client for all packages.

This is the reason why you have to install an HTTP client or adapter before you would be able to install the SDK. You
can find the complete list of available clients and adapters [here](http://docs.php-http.org/en/latest/clients.html).

So if you prefer Guzzle 6 then you can install this library like this:

```
$ composer require php-http/guzzle6-adapter:^1.1.1
$ composer require apigee/edge:2.*

```

Usage examples
----------

### Basic API usage


```php
<?php

use Apigee\Edge\Api\Management\Controller\DeveloperController;
use Apigee\Edge\Api\Management\Entity\Developer;
use Apigee\Edge\Exception\ApiException;
use Apigee\Edge\Exception\ClientErrorException;
use Apigee\Edge\Exception\ServerErrorException;
use Apigee\Edge\HttpClient\Client;
use Http\Message\Authentication\BasicAuth;

include_once 'vendor/autoload.php';

$username = 'my-email-address@example.com';
$password = 'my-secure-password';
$organization = 'my-organization';

$auth = new BasicAuth($username, $password);
// Initialize a client and use basic authentication for all API calls.
$client = new Client($auth);

// Initialize a controller for making API calls, for example a developer controller to working with developer entities.
$ec = new DeveloperController($organization, $client);

try {
    /** @var \Apigee\Edge\Api\Management\Entity\Developer $entity */
    $entity = $ec->load('developer1@example.com');
    $entity->setEmail('developer2@example.com');
    $ec->update($entity);
    // Some setters on entities are intentionally marked as @internal because the underlying entity properties can not
    // be changed on the entity level. Those must be modified by using dedicated API calls.
    // So instead of this:
    $entity->setStatus(Developer::STATUS_INACTIVE);
    // You should use this:
    $ec->setStatus($entity->id(), Developer::STATUS_INACTIVE);
} catch (ClientErrorException $e) {
    // HTTP code >= 400 and < 500. Ex.: 401 Unauthorised.
    if ($e->getEdgeErrorCode()) {
        print $e->getEdgeErrorCode();
    } else {
        print $e;
    }
} catch (ServerErrorException $e) {
    // HTTP code >= 500 and < 600. Ex.: 500 Server error.
} catch (ApiException $e) {
    // Anything else, because this is the parent class of all the above.
}

```

### Advanced examples

* [Create new developer app with a custom- and an auto-generated credential](examples/create_new_app_with_credential.php)
* [Developer app analytics](examples/developer_app_analytics.php)
* [List entities (developers, api products, developer apps, etc.)](examples/list_multiple_entities.php)

Unit Tests
----------

Setup the test suite using [Composer](http://getcomposer.org/) if has not already done:

```
$ composer install --dev
```

Run it using [PHPUnit](http://phpunit.de/):

```
$ composer test
```

Testing of new changes does not require Apigee Edge connection. By default, unit tests are using the content of the
[offline-test-data](tests/offline-test-data) folder to make testing quicker and easier. If you would like to run units
tests with a real Apigee Edge instance you have to specify the following environment variables (without brackets):

```shell
APIGEE_EDGE_PHP_SDK_HTTP_CLIENT=\Http\Adapter\Guzzle6\Client
APIGEE_EDGE_PHP_SDK_BASIC_AUTH_USER=[YOUR-EMAIL-ADDRESS@HOST.COM]
APIGEE_EDGE_PHP_SDK_BASIC_AUTH_PASSWORD=[PASSWORD]
APIGEE_EDGE_PHP_SDK_ORGANIZATION=[ORGANIZATION]
APIGEE_EDGE_PHP_SDK_ENVIRONMENT=[ENVIRONMENT]
```

There are multiple ways to set these environment variables, but probably the easiest is creating a copy from the
phpunit.xml.dist file as phpunit.xml and add these variables one-by-one inside the [<php> element](https://phpunit.de/manual/current/en/appendixes.configuration.html#appendixes.configuration.php-ini-constants-variables)
with an <env> element.

It is also possible to create and use your own data set. If you would like to use your own offline test data set then
you just need to define the `APIGEE_EDGE_PHP_SDK_OFFLINE_TEST_DATA_FOLDER` environment variable set its value to the parent
folder of your own test data set.

PS.: Some unit tests can not be executed when the offline test data is in use, those are automatically marked as skipped.
