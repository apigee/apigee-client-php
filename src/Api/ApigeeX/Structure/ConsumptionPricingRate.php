<?php

/*
 * Copyright 2021 Google LLC
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

namespace Apigee\Edge\Api\ApigeeX\Structure;

use Apigee\Edge\Structure\BaseObject;

/**
 * Class ConsumptionPricingRate.
 *
 * TO: Add documentation link
 */
final class ConsumptionPricingRate extends BaseObject
{
    /**
     * Value of "end" from the API response.
     *
     * TODO Can this be null?
     *
     * @var string|null
     */
    private $end;

    /**
     * Value of "start" from the API response,.
     *
     * @var string
     */
    private $start;

    /**
     * @var \Apigee\Edge\Api\ApigeeX\Structure\Fee
     */
    private $fee = [];

    /**
     * @return string|null
     */
    public function getEnd(): ?string
    {
        return $this->end;
    }

    /**
     * @param string $end
     */
    public function setEnd(string $end): void
    {
        $this->end = $end;
    }

    /**
     * @return string|null
     */
    public function getStart(): ?string
    {
        return $this->start;
    }

    /**
     * @param string $start
     */
    public function setStart(string $start): void
    {
        $this->start = $start;
    }

    /**
     * @return \Apigee\Edge\Api\ApigeeX\Structure\Fee|null
     */
    public function getFee(): ?Fee
    {
        return $this->fee;
    }

    /**
     * @param \Apigee\Edge\Api\ApigeeX\Structure\Fee $fee
     */
    public function setFee(Fee $fee): void
    {
        $this->fee = $fee;
    }
}
