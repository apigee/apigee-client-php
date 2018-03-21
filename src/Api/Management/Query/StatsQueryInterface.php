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

namespace Apigee\Edge\Api\Management\Query;

use League\Period\Period;

/**
 * Interface StatsQueryInterface.
 *
 * Represents a custom query that can be sent to the Stats API.
 *
 * @see https://docs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/environments/%7Benv_name%7D/stats/%7Bdimension_name%7D-0
 */
interface StatsQueryInterface
{
    /**
     * @return string[]
     */
    public function getMetrics(): array;

    /**
     * @param string[] $metrics
     *
     * @return StatsQueryInterface
     */
    public function setMetrics(array $metrics): StatsQueryInterface;

    /**
     * @return Period
     */
    public function getTimeRange(): Period;

    /**
     * @param Period $timeRange
     *
     * @return StatsQueryInterface
     */
    public function setTimeRange(Period $timeRange): StatsQueryInterface;

    /**
     * @return null|string
     */
    public function getTimeUnit(): ?string;

    /**
     * @param null|string $timeUnit
     *
     * @return StatsQueryInterface
     */
    public function setTimeUnit(?string $timeUnit): StatsQueryInterface;

    /**
     * @return null|string
     */
    public function getSortBy(): ?string;

    /**
     * @param null|string $sortBy
     *
     * @return StatsQueryInterface
     */
    public function setSortBy(?string $sortBy): StatsQueryInterface;

    /**
     * @return null|string
     */
    public function getSort(): ?string;

    /**
     * @param null|string $sort
     *
     * @return StatsQueryInterface
     */
    public function setSort(?string $sort): StatsQueryInterface;

    /**
     * @return int|null
     */
    public function getTopK(): ?int;

    /**
     * @param int|null $topK
     *
     * @return StatsQueryInterface
     */
    public function setTopK(?int $topK): StatsQueryInterface;

    /**
     * @return int|null
     */
    public function getLimit(): ?int;

    /**
     * @param int|null $limit
     *
     * @return StatsQueryInterface
     */
    public function setLimit(?int $limit): StatsQueryInterface;

    /**
     * @return int|null
     */
    public function getOffset(): ?int;

    /**
     * @param int|null $offset
     *
     * @return StatsQueryInterface
     */
    public function setOffset(?int $offset): StatsQueryInterface;

    /**
     * @return bool|null
     */
    public function getRealtime(): ?bool;

    /**
     * @param bool|null $realtime
     *
     * @return StatsQueryInterface
     */
    public function setRealtime(?bool $realtime): StatsQueryInterface;

    /**
     * @return int|null
     */
    public function getAccuracy(): ?int;

    /**
     * @param int|null $accuracy
     *
     * @return StatsQueryInterface
     */
    public function setAccuracy(?int $accuracy): StatsQueryInterface;

    /**
     * @return bool
     */
    public function getTsAscending(): bool;

    /**
     * @param bool $tsAscending
     *
     * @return StatsQueryInterface
     */
    public function setTsAscending(bool $tsAscending): StatsQueryInterface;

    /**
     * @return null|string
     */
    public function getFilter(): ?string;

    /**
     * @param null|string $filter
     *
     * @return StatsQuery
     *
     * @see https://docs.apigee.com/analytics-services/reference/analytics-reference#filters
     */
    public function setFilter(?string $filter): StatsQueryInterface;
}
