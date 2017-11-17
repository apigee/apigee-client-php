# Apigee Edge PHP SDK 2.x
Version 2 of the Apigee Edge PHP SDK. 

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

PS.: Some unit tests can not be executed when the offline test data is used, those are automatically skipped.