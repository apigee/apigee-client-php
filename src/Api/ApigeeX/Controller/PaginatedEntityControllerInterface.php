<?php

/*
 * Copyright 2023 Google LLC
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

namespace Apigee\Edge\Api\ApigeeX\Controller;

use Apigee\Edge\Api\ApigeeX\Structure\PagerInterface;

/**
 * Interface PaginatedEntityControllerInterface.
 *
 * For those controllers that contains as least one method that communicates
 * with an endpoint which supports pagination.
 */
interface PaginatedEntityControllerInterface
{
    /**
     * Creates a pager.
     *
     * @param int $limit
     *   Number of items to return. Default is 0 which means load as much as
     *   supported. (Different endpoints have different limits, ex.:
     *   100 for AppGroup apps.)
     * @param string|null $pageToken
     *   First item in the list, if it is not set then Apigee Edge decides the
     *   first item.
     *
     * @return \Apigee\Edge\Api\ApigeeX\Structure\PagerInterface
     *   The pager object.
     */
    public function createPager(int $limit = 0, string $pageToken = null): PagerInterface;
}
