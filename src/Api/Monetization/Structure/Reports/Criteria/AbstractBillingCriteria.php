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

namespace Apigee\Edge\Api\Monetization\Structure\Reports\Criteria;

/**
 * Base class for billing report queries (billing and prepaid balance).
 *
 * @internal
 */
abstract class AbstractBillingCriteria extends AbstractCriteria
{
    /**
     * English name of a month with capital letters, ex.: NOVEMBER.
     *
     * @var string
     */
    protected $billingMonth;

    /**
     * @var int
     */
    protected $billingYear;

    /**
     * AbstractBillingReportQuery constructor.
     *
     * @param string $billingMonth
     *   Billing month name with capital letters, ex. JULY.
     * @param $billingYear
     *   Billing year.
     */
    public function __construct(string $billingMonth, int $billingYear)
    {
        $this->billingMonth = $billingMonth;
        $this->billingYear = $billingYear;
    }

    /**
     * @param string $billingMonth
     *
     * @return self
     */
    public function setBillingMonth(string $billingMonth): self
    {
        $this->billingMonth = $billingMonth;

        return $this;
    }

    /**
     * @param int $billingYear
     *
     * @return self
     */
    public function setBillingYear(int $billingYear): self
    {
        $this->billingYear = $billingYear;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillingMonth(): string
    {
        return $this->billingMonth;
    }

    /**
     * @return int
     */
    public function getBillingYear(): int
    {
        return $this->billingYear;
    }
}
