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

use DateTimeImmutable;

final class RevenueReportCriteria extends AbstractCriteria
{
    use GroupByCriteriaTrait;
    use TransactionTypesCriteriaTrait;

    /**
     * @var DateTimeImmutable
     */
    private $fromDate;

    /**
     * @var DateTimeImmutable|null
     */
    private $toDate;

    /**
     * Array of developer attributes to be displayed in the report.
     *
     * According to MINT engineers this defaults to "none".
     *
     * "devCustomAttributes" in the API request.
     *
     * @var string[]
     */
    private $developerAttributes = [];

    /**
     * Array of transaction attributes to be displayed in the report.
     *
     * According to MINT engineers this defaults to "all".
     *
     * "transactionCustomAttributes" in the API request.
     *
     * @var string[]
     */
    private $transactionAttributes = [];

    /**
     * RevenueReportCriteria constructor.
     *
     * @param DateTimeImmutable $fromDate
     * @param DateTimeImmutable|null $toDate
     */
    public function __construct(DateTimeImmutable $fromDate, ?DateTimeImmutable $toDate = null)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getFromDate(): DateTimeImmutable
    {
        return $this->fromDate;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getToDate(): ?DateTimeImmutable
    {
        return $this->toDate;
    }

    /**
     * @return string[]
     */
    public function getDeveloperAttributes(): array
    {
        return $this->developerAttributes;
    }

    /**
     * @param string ...$developerAttributes
     * return \self
     */
    public function developerAttributes(string ...$developerAttributes): self
    {
        $this->developerAttributes = $developerAttributes;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getTransactionAttributes(): array
    {
        return $this->transactionAttributes;
    }

    /**
     * @param string ...$transactionAttributes
     *
     * @return self
     */
    public function transactionAttributes(string ...$transactionAttributes): self
    {
        $this->transactionAttributes = $transactionAttributes;

        return $this;
    }
}
