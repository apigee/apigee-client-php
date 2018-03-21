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

namespace Apigee\Edge\Tests\Api\Management\Query;

use Apigee\Edge\Api\Management\Query\StatsQuery;
use Apigee\Edge\Api\Management\Query\StatsQueryNormalizer;
use League\Period\Period;
use PHPUnit\Framework\TestCase;

/**
 * Class StatsQueryNormalizerTest.
 *
 * @group normalizer
 * @group query
 * @group offline
 * @small
 */
class StatsQueryNormalizerTest extends TestCase
{
    /** @var \Apigee\Edge\Api\Management\Query\StatsQueryNormalizer */
    private static $normalizer;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        static::$normalizer = new StatsQueryNormalizer();
        parent::setUpBeforeClass();
    }

    public function testNormalizationWithMinimumData(): void
    {
        date_default_timezone_set('Europe/Budapest');
        $q = new StatsQuery(['metric1', 'metric2'], Period::createFromMonth(2018, 1));
        $data = static::$normalizer->normalize($q);
        $expected = [
            'select' => 'metric1,metric2',
            // CET to UTC conversion test.
            'timeRange' => '12/31/2017 23:00~01/31/2018 23:00',
            'tsAscending' => 'false',
        ];
        $this->assertEquals($expected, $data);
    }

    public function testNormalizationWithAllParameters(): void
    {
        date_default_timezone_set('UTC');
        $q = new StatsQuery(['metric1', 'metric2'], Period::createFromMonth(2018, 1));
        $q->setSort(StatsQuery::SORT_ASC);
        $sortBy = 'metric1';
        $timeUnit = 'day';
        $filter = 'filter1';
        $realtime = true;
        $accuracy = 1;
        $offset = 10;
        $limit = 10;
        $topK = 10;
        $tsAscending = true;
        $q->setSortBy($sortBy);
        $q->setTimeUnit($timeUnit);
        $q->setFilter($filter);
        $q->setRealtime($realtime);
        $q->setAccuracy($accuracy);
        $q->setOffset($offset);
        $q->setLimit($limit);
        $q->setTopK($topK);
        $q->setTsAscending($tsAscending);
        $data = static::$normalizer->normalize($q);

        $expected = [
            'filter' => 'filter1',
            'timeUnit' => 'day',
            'sort' => 'ASC',
            'limit' => 10,
            'realtime' => 'true',
            'accuracy' => 1,
            'offset' => 10,
            'tsAscending' => 'true',
            'select' => 'metric1,metric2',
            'timeRange' => '01/01/2018 00:00~02/01/2018 00:00',
            'sortby' => 'metric1',
            'topk' => 10,
        ];

        $this->assertEquals($expected, $data);
    }
}
