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
 * Interface PaginatedEntityIdListingControllerInterface.
 *
 * For those entities that can be listed as objects or by their entity ids and
 * pagination is supported on their endpoint.
 */
interface PaginatedEntityIdListingControllerInterface extends PaginatedEntityControllerInterface
{
    /**
     * Returns list of entity ids from Apigee X. The returned number of
     * entity ids can be limited, if you do not provide a limit then all entity
     * ids are returned.
     *
     * On pagination enabled endpoints Apigee X only
     * returns certain number of entities in one API call so we have to collect
     * all entities by sending multiple API calls to Apigee X synchronously.
     * If you do not actually need _all_ entities of a type then always set
     * a limit to reduce memory usage and increase speed.
     *
     * @param \Apigee\Edge\Api\ApigeeX\Structure\PagerInterface|null $pager
     *   Pager.
     *
     * @return array
     */
    public function getEntityIds(PagerInterface $pager = null): array;
}
