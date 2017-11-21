# Apigee Edge PHP SDK

**Currently the 2.x version is under active development. Use it on your own risk or install the latest 1.x version!**

Getting started
---------- 

The APIgee PHP Edge SDK is an HTTP client implementation-independent library thanks for the [HTTPlug](http://docs.php-http.org/en/latest/)
library. It means that you, as a developer, can choose the client that best fits your project and use the same
client for all packages.

This is the reason why you have to install an HTTP client or adapter before you would be able to install the SDK. You
can find the complete list of available clients and adapters [here](http://docs.php-http.org/en/latest/clients.html).

So if you prefer Guzzle 6 then you can install this library like this:

```
$ composer require php-http/guzzle6-adapter
$ composer require apigee/edge:2.*
```

Unit Tests
----------

Setup the test suite using [Composer](http://getcomposer.org/) if not already done:

```
$ composer install --dev
```

Run it using [PHPUnit](http://phpunit.de/):

```
$ composer test
```

Testing of new changes does not require Apigee Edge connection. By default, unit tests are using the content of the
[offline-test-data](offline-test-data) folder to make testing quicker and easier. If you would like to run units tests
with a real Apigee Edge instance you have to specify the following environment variables (without brackets):

```shell
APIGEE_PHP_SDK_HTTP_CLIENT=\Http\Adapter\Guzzle6\Client
APIGEE_PHP_SDK_BASIC_AUTH_USER=[YOUR-EMAIL-ADDRESS@HOST.COM]
APIGEE_PHP_SDK_BASIC_AUTH_PASSWORD=[PASSWORD]
APIGEE_PHP_SDK_ORGANIZATION=[ORGANIZATION]
```

There are multiple ways to set these environment variables, but probably the easiest is creating a copy from the
phpunit.xml.dist file as phpunit.xml and add these variables one-by-one inside the [<php> element](https://phpunit.de/manual/current/en/appendixes.configuration.html#appendixes.configuration.php-ini-constants-variables)
with an <env> element.

It is also possible to create and use your own data set. If you would like to use your own offline test data set then
you just need to define the `APIGEE_PHP_SDK_OFFLINE_TEST_DATA_FOLDER` environment variable set its value to the parent
folder of your own test data set.

PS.: Some unit tests can not be executed when the offline test data is used, those are automatically skipped.