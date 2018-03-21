<?php

/*
 * Copyright 2018 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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
