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

namespace Apigee\Edge\Api\Monetization\Entity;

/**
 * Represents an accepted rate plan by a company.
 */
class CompanyAcceptedRatePlan extends AcceptedRatePlan implements CompanyAcceptedRatePlanInterface
{
    /** @var \Apigee\Edge\Api\Monetization\Entity\Company */
    protected $company;

    /**
     * @inheritdoc
     */
    public function getCompany(): CompanyInterface
    {
        return $this->company;
    }

    /**
     * @inheritdoc
     */
    public function setCompany(CompanyInterface $company): void
    {
        $this->company = $company;
    }
}