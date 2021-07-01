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
 * Class StatsQuery.
 */
class StatsQuery implements StatsQueryInterface
{
    const SORT_ASC = 'ASC';

    const SORT_DESC = 'DESC';

    /** @var string[] */
    private $metrics = [];

    /** @var \League\Period\Period */
    private $timeRange;

    /** @var string|null */
    private $filter;

    /** @var string|null */
    private $timeUnit;

    /** @var string|null */
    private $sortBy;

    /** @var string|null */
    private $sort;

    /** @var int|null */
    private $topK;

    /** @var int|null */
    private $limit;

    /** @var int|null */
    private $offset;

    /** @var bool|null */
    private $realtime;

    /** @var int|null */
    private $accuracy;

    /** @var bool */
    private $tsAscending = false;

    /**
     * StatsQuery constructor.
     *
     * @param string[] $metrics
     *   Metrics to be aggregated for the report.
     * @param \League\Period\Period $timeRange
     *   The start and end time for the desired interval.
     */
    public function __construct(array $metrics, Period $timeRange)
    {
        $this->metrics = $metrics;
        $this->timeRange = $timeRange;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilter(): ?string
    {
        return $this->filter;
    }

    /**
     * {@inheritdoc}
     */
    public function setFilter(?string $filter): StatsQueryInterface
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetrics(): array
    {
        return $this->metrics;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetrics(array $metrics): StatsQueryInterface
    {
        $this->metrics = $metrics;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeRange(): Period
    {
        return $this->timeRange;
    }

    /**
     * {@inheritdoc}
     */
    public function setTimeRange(Period $timeRange): StatsQueryInterface
    {
        $this->timeRange = $timeRange;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeUnit(): ?string
    {
        return $this->timeUnit;
    }

    /**
     * {@inheritdoc}
     */
    public function setTimeUnit(?string $timeUnit): StatsQueryInterface
    {
        $this->timeUnit = $timeUnit;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSortBy(): ?string
    {
        return $this->sortBy;
    }

    /**
     * {@inheritdoc}
     */
    public function setSortBy(?string $sortBy): StatsQueryInterface
    {
        $this->sortBy = $sortBy;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSort(): ?string
    {
        return $this->sort;
    }

    /**
     * {@inheritdoc}
     */
    public function setSort(?string $sort): StatsQueryInterface
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTopK(): ?int
    {
        return $this->topK;
    }

    /**
     * {@inheritdoc}
     */
    public function setTopK(?int $topK): StatsQueryInterface
    {
        $this->topK = $topK;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * {@inheritdoc}
     */
    public function setLimit(?int $limit): StatsQueryInterface
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * {@inheritdoc}
     */
    public function setOffset(?int $offset): StatsQueryInterface
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRealtime(): ?bool
    {
        return $this->realtime;
    }

    /**
     * {@inheritdoc}
     */
    public function setRealtime(?bool $realtime): StatsQueryInterface
    {
        $this->realtime = $realtime;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccuracy(): ?int
    {
        return $this->accuracy;
    }

    /**
     * {@inheritdoc}
     */
    public function setAccuracy(?int $accuracy): StatsQueryInterface
    {
        $this->accuracy = $accuracy;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTsAscending(): bool
    {
        return $this->tsAscending;
    }

    /**
     * {@inheritdoc}
     */
    public function setTsAscending(bool $tsAscending): StatsQueryInterface
    {
        $this->tsAscending = $tsAscending;

        return $this;
    }
}
