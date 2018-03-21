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

namespace Apigee\Edge\Structure;

/**
 * Class CpsListLimitInterface.
 */
interface CpsListLimitInterface
{
    /**
     * @return string The primary key of the entity that the list will start.
     */
    public function getStartKey(): string;

    /**
     * @param string $startKey
     *    The primary key of the entity that the list will start.
     *
     * @return string
     */
    public function setStartKey(string $startKey): string;

    /**
     * @return int Number of entities to return.
     */
    public function getLimit(): int;

    /**
     * @param int $limit
     *   Number of entities to return.
     *
     * @return int
     */
    public function setLimit(int $limit): int;
}
