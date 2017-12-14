<?php

namespace Apigee\Edge\Tests\Test\Mock;

use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;

/**
 * Interface MockClientInterface.
 *
 * Creates a common interface that can be implemented by Mock http clients until this PR is not going to be merged.
 *
 * @see https://github.com/php-http/mock-client/pull/24
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface MockClientInterface extends HttpClient, HttpAsyncClient
{
}
