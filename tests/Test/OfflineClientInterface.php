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

namespace Apigee\Edge\Tests\Test;

use Apigee\Edge\ClientInterface;
use Apigee\Edge\Tests\Test\HttpClient\MockHttpClientInterface;

/**
 * Base interface for those test clients that does not call Apigee Edge.
 */
interface OfflineClientInterface extends ClientInterface
{
    public const USER_AGENT_PREFIX = 'OFFLINE';

    /**
     * Exposes the underlying mock http client.
     *
     * @return \Apigee\Edge\Tests\Test\HttpClient\MockHttpClientInterface
     */
    public function getMockHttpClient(): MockHttpClientInterface;
}
