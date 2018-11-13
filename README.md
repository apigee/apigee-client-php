# Apigee Edge Client Library for PHP

[![Build Status](https://travis-ci.org/apigee/apigee-client-php.svg?branch=2.x)](https://travis-ci.org/apigee/apigee-client-php)
[![Code Coverage](https://codecov.io/gh/apigee/apigee-client-php/branch/2.x/graph/badge.svg)](https://codecov.io/gh/apigee/apigee-client-php/branch/2.x-dev)
[![Latest Stable Version](https://poser.pugx.org/apigee/apigee-client-php/v/stable)](https://packagist.org/packages/apigee/apigee-client-php)
[![Total Downloads](https://poser.pugx.org/apigee/apigee-client-php/downloads)](https://packagist.org/packages/apigee/apigee-client-php)
[![Latest Unstable Version](https://img.shields.io/badge/unstable-2.0.x--dev-orange.svg?style=flat-square)](https://packagist.org/packages/apigee/apigee-client-php)
[![Minimum PHP Version](https://img.shields.io/badge/PHP-%3E%3D%207.1-8892BF.svg?style=flat-square)](https://php.net/)
[![License](https://poser.pugx.org/apigee/apigee-client-php/license)](https://packagist.org/packages/apigee/apigee-client-php)

The Apigee API Client Library for PHP makes it easy to develop PHP clients that call the Apigee Edge Management API. The
Apigee API Client Library for PHP  enables you to interact with the API using objects instead of coding to handle the
HTTP request and response calls directly.

Specifically, the Apigee API Client Library for PHP provides access to Apigee Edge Management APIs in the following
categories:

* [API Products](https://apidocs.apigee.com/api/api-products-1)
* [Apps](https://apidocs.apigee.com/api/apps-0)
* [Apps: Company](https://apidocs.apigee.com/api/apps-company)
* [Apps: Developer](https://apidocs.apigee.com/api/apps-developer)
* [Companies](https://apidocs.apigee.com/api/companies-0)
* [Company App Keys](https://apidocs.apigee.com/api/company-app-keys-0)
* [Company Developers](https://apidocs.apigee.com/api/company-developers-0)
* [Developer App Keys](https://apidocs.apigee.com/api/developer-app-keys)
* [Developers](https://apidocs.apigee.com/api/developers-0)
* [Stats](https://apidocs.apigee.com/api/stats)

For more information about the Apigee Edge Management APIs, see [Getting started with the API Edge Management APIs](https://apidocs.apigee.com/api-reference/content/api-reference-getting-started)
in the Apigee documentation.

The Apigee API Client Library for PHP, built using the HTTPlug library, provides an HTTP client
implementation-independent library. You choose the client that best fits your project requirements.

If you need PHP < 7.1 or Monetization API support please install the older [edge-php-sdk version](https://github.com/apigee/edge-php-sdk).
We are planning to add Monetization API support to this library in the near future.

## Monetization API: Alpha Release

The [Apigee Monetization APIs](https://apidocs.apigee.com/api-reference/content/monetization-apis) have been added to this library but are
considered to be an alpha.  If you run into any issues, add an issue to our [GitHub issue queue](https://github.com/apigee/apigee-client-php/issues).

## Installing the client library

You must install an HTTP client or adapter before you install the Apigee API Client Library for PHP. For a complete list
of available clients and adapters, see [Clients & Adapters](http://docs.php-http.org/en/latest/clients.html) in the
PHP-HTTP documentation.

To install the client library using Guzzle 6, enter the following commands:

```
$ composer require php-http/guzzle6-adapter:^1.1.1
$ composer require apigee/apigee-client-php
```

If you would like to use [OAuth (SAML) authentication](https://docs.apigee.com/api-platform/system-administration/using-oauth2-security-apigee-edge-management-api#usingtheapitogettokens-postrefreshanaccesstoken)
then you have to install [this patch](https://patch-diff.githubusercontent.com/raw/php-http/client-common/pull/103.diff)
from [this php-http/client-common pull request](https://github.com/php-http/client-common/pull/103). If you use
[composer-patches](https://github.com/cweagans/composer-patches) plugin and you [allowed dependencies to apply patches](https://github.com/cweagans/composer-patches#allowing-patches-to-be-applied-from-dependencies)
then this patch is automatically gets applied when this library is installed.

## Usage examples

### Basic API usage


```php
<?php

use Apigee\Edge\Api\Management\Controller\DeveloperController;
use Apigee\Edge\Api\Management\Entity\Developer;
use Apigee\Edge\Exception\ApiException;
use Apigee\Edge\Exception\ClientErrorException;
use Apigee\Edge\Exception\ServerErrorException;
use Apigee\Edge\Client;
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

## Unit Tests

Setup the test suite using [Composer](http://getcomposer.org/) if it has not already done:

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
APIGEE_EDGE_PHP_CLIENT_API_CLIENT=\Apigee\Edge\Tests\Test\FileSystemMockClient
APIGEE_EDGE_PHP_CLIENT_HTTP_CLIENT=\Http\Adapter\Guzzle6\Client
APIGEE_EDGE_PHP_CLIENT_BASIC_AUTH_USER=[YOUR-EMAIL-ADDRESS@HOST.COM]
APIGEE_EDGE_PHP_CLIENT_BASIC_AUTH_PASSWORD=[PASSWORD]
APIGEE_EDGE_PHP_CLIENT_ORGANIZATION=[ORGANIZATION]
APIGEE_EDGE_PHP_CLIENT_ENVIRONMENT=[ENVIRONMENT]
```

There are multiple ways to set these environment variables, but probably the easiest is creating a copy from the
phpunit.xml.dist file as phpunit.xml and add these variables one-by-one inside the [<php> element](https://phpunit.de/manual/current/en/appendixes.configuration.html#appendixes.configuration.php-ini-constants-variables)
with an <env> element.

It is also possible to create and use your own data set. If you would like to use your own offline test data set then
you just need to define the `APIGEE_EDGE_PHP_CLIENT_OFFLINE_TEST_DATA_FOLDER` environment variable set its value to the parent
folder of your own test data set.

PS.: Some unit tests cannot be executed when the offline test data set is in use, those are automatically marked as
skipped.

## Support

This is not an officially supported Google product. Please file issues in our GitHub Issue Queue. We would love to
accept contributions to this project, please see the [contribution guidelines for this project](CONTRIBUTING.md) for
more details.
