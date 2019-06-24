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

namespace Apigee\Edge\Api\Monetization\Structure;

use Apigee\Edge\Api\Monetization\Entity\Property\IdPropertyAwareTrait;
use Apigee\Edge\Api\Monetization\Entity\Property\IdPropertyInterface;
use Apigee\Edge\Structure\BaseObject;

abstract class RatePlanRate extends BaseObject implements IdPropertyInterface
{
    use IdPropertyAwareTrait;

    public const TYPE_REVSHARE = 'REVSHARE';

    public const TYPE_RATECARD = 'RATECARD';

    /** @var int */
    private $startUnit;

    /** @var int */
    private $endUnit;

    /**
     * @return int
     */
    public function getStartUnit(): int
    {
        return $this->startUnit;
    }

    /**
     * @param int $startUnit
     */
    public function setStartUnit(int $startUnit): void
    {
        $this->startUnit = $startUnit;
    }

    /**
     * @return int
     */
    public function getEndUnit(): ?int
    {
        return $this->endUnit;
    }

    /**
     * @param int $endUnit
     */
    public function setEndUnit(int $endUnit): void
    {
        $this->endUnit = $endUnit;
    }
}
