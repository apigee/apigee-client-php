<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
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
