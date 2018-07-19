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
     * Creates a CPS limit if it is supported on the organization.
     *
     * @param int $limit
     *   Number of items to return. Default is 0 which means load as much as
     *   supported. (Different endpoints have different limits, ex.:
     *   1000 for API products, 100 for Company apps.)
     * @param null|string $startKey
     *   First item in the list, if it is not set then Apigee Edge decides the
     *   first item.
     *
     * @throws \Apigee\Edge\Exception\CpsNotEnabledException
     *   If CPS listing is not supported on the organization.
     *
     * @return \Apigee\Edge\Structure\CpsListLimitInterface
     *   CPS limit object.
     *
     * @see https://docs.apigee.com/api-services/content/api-reference-getting-started#cps
     */
    public function createCpsLimit(int $limit = 0, ?string $startKey = null): CpsListLimitInterface;
}
