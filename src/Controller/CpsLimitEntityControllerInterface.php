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

namespace Apigee\Edge\Controller;

use Apigee\Edge\Structure\CpsListLimitInterface;

/**
 * Interface CpsLimitEntityControllerInterface.
 *
 * For entities that supports CPS limit in their listing API calls, ex.: developer.
 *
 * @see https://docs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/developers
 */
interface CpsLimitEntityControllerInterface
{
    /**
     * Returns a representation of a Core Persistence Services (CPS) limit.
     *
     * This limit can be used list API calls on Edge to limit the number of returned
     * results but CPS is not enabled on all organisations.
     *
     * @param string $startKey
     *    The primary key of the entity that the list should start.
     * @param int $limit
     *    Number of entities to return.
     *
     * @return CpsListLimitInterface
     *
     * @see https://docs.apigee.com/api-services/content/api-reference-getting-started#cps
     */
    public function createCpsLimit(string $startKey, int $limit): CpsListLimitInterface;
}
