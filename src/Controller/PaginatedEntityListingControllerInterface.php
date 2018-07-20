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

use Apigee\Edge\Structure\PagerInterface;

/**
 * Interface PaginatedEntityListingControllerInterface.
 */
interface PaginatedEntityListingControllerInterface
{
    /**
     * Returns list of entities fro Apigee Edge. The returned number of entities
     * can be limited, if you do not provide a limit then all entities are
     * returned.
     *
     * On CPS enabled orgs and pagination enabled endpoints Apigee Edge only
     * returns certain number of entities in one API call so we have to collect
     * all entities by sending multiple API calls to Apigee Edge synchronously.
     * If you do not actually need _all_ entities of a type then always set
     * a limit to reduce memory usage and increase speed.
     *
     * @param \Apigee\Edge\Structure\PagerInterface|null $pager
     *
     * @return \Apigee\Edge\Entity\EntityInterface[]
     */
    public function getEntities(PagerInterface $pager = null): array;

    /**
     * Returns list of entity ids from Apigee Edge. The returned number of
     * entity ids can be limited, if you do not provide a limit then all entity
     * ids are returned.
     *
     * On CPS enabled orgs and pagination enabled endpoints Apigee Edge only
     * returns certain number of entities in one API call so we have to collect
     * all entities by sending multiple API calls to Apigee Edge synchronously.
     * If you do not actually need _all_ entities of a type then always set
     * a limit to reduce memory usage and increase speed.
     *
     * @param \Apigee\Edge\Structure\PagerInterface|null $pager
     *
     * @return array
     */
    public function getEntityIds(PagerInterface $pager = null): array;

    /**
     * Creates a pager if CPS is supported on the organization.
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
     * @return PagerInterface
     *   The pager object.
     */
    public function createPager(int $limit = 0, ?string $startKey = null): PagerInterface;
}
