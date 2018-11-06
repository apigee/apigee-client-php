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

namespace Apigee\Edge\Tests\Test\Controller;

use Apigee\Edge\ClientInterface;

trait EntityControllerAwareTestTrait
{
    /**
     * Returns the decorated entity controller used in a test.
     *
     * @param \Apigee\Edge\ClientInterface|null $client
     *
     * @return \Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface
     */
    abstract protected static function entityController(ClientInterface $client = null): EntityControllerTesterInterface;
}
