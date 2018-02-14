<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Tests\Test\Mock;

use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;

/**
 * Interface MockClientInterface.
 *
 * Creates a common interface that can be implemented by Mock http clients until this PR is not going to be merged.
 *
 * @see https://github.com/php-http/mock-client/pull/24
 */
interface MockClientInterface extends HttpClient, HttpAsyncClient
{
}
