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

class Developer extends LegalEntity implements DeveloperInterface
{
    /**
     * Value of "parent" from the API response.
     *
     * #FIXME
     * This value only contains a reference to the company set in the
     * MINT_COMPANY_ID. (It could happen that the developer is not actually
     * member of the referenced company.)
     *
     * @var null|\Apigee\Edge\Api\Monetization\Entity\Company
     */
    protected $company;

    /**
     * @return \Apigee\Edge\Api\Monetization\Entity\Company|null
     */
    public function getCompany(): ?Company
    {
        return $this->company;
    }

    /**
     * @param \Apigee\Edge\Api\Monetization\Entity\Company $company
     */
    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }
}
