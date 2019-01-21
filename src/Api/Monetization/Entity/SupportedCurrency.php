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

use Apigee\Edge\Api\Monetization\Entity\Property\VirtualCurrencyPropertyAwareTrait;
use Apigee\Edge\Entity\Property\DescriptionPropertyAwareTrait;
use Apigee\Edge\Entity\Property\DisplayNamePropertyAwareTrait;
use Apigee\Edge\Entity\Property\NamePropertyAwareTrait;
use Apigee\Edge\Entity\Property\StatusPropertyAwareTrait;

class SupportedCurrency extends OrganizationAwareEntity implements SupportedCurrencyInterface
{
    use DescriptionPropertyAwareTrait;
    use DisplayNamePropertyAwareTrait;
    use NamePropertyAwareTrait;
    use StatusPropertyAwareTrait {
        setStatus as private traitSetStatus;
    }
    use VirtualCurrencyPropertyAwareTrait;

    /** @var float|null */
    protected $creditLimit;

    /** @var float|null */
    protected $minimumTopUpAmount;

    /**
     * @inheritdoc
     */
    public function getCreditLimit(): ?float
    {
        return $this->creditLimit;
    }

    /**
     * @inheritdoc
     */
    public function setCreditLimit(float $creditLimit): void
    {
        $this->creditLimit = $creditLimit;
    }

    /**
     * @inheritdoc
     */
    public function setStatus(string $status): void
    {
        // This is not an internal method in this case so we had to override
        // the inherited one from the trait.
        $this->traitSetStatus($status);
    }

    /**
     * @inheritdoc
     */
    public function getMinimumTopUpAmount(): ?float
    {
        return $this->minimumTopUpAmount;
    }

    /**
     * @inheritdoc
     */
    public function setMinimumTopUpAmount(float $minimumTopUpAmount): void
    {
        $this->minimumTopUpAmount = $minimumTopUpAmount;
    }
}
