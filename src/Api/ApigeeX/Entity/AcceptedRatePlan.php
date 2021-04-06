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

namespace Apigee\Edge\Api\ApigeeX\Entity;

use Apigee\Edge\Api\ApigeeX\Entity\Property\ApiProductPropertyAwareTrait;
use Apigee\Edge\Api\ApigeeX\Entity\Property\EndTimePropertyAwareTrait;
use Apigee\Edge\Api\ApigeeX\Entity\Property\StartTimePropertyAwareTrait;
use Apigee\Edge\Api\ApigeeX\Entity\RatePlanInterface;
use Apigee\Edge\Api\Monetization\Entity\Entity;
use Apigee\Edge\Entity\Property\NamePropertyAwareTrait;

abstract class AcceptedRatePlan extends Entity implements AcceptedRatePlanInterface
{
    use EndTimePropertyAwareTrait;
    use StartTimePropertyAwareTrait;
    use ApiProductPropertyAwareTrait;
    use NamePropertyAwareTrait;

    /** @var string */
    protected $createdAt;

    /** @var string */
    protected $lastModifiedAt;

     /** @var \Apigee\Edge\Api\ApigeeX\Entity\RatePlanInterface */
    protected $ratePlan;

    /**
     * {@inheritdoc}
     */
    public function getLastModifiedAt(): ?string
    {
        return $this->lastModifiedAt;
    }

    /**
     * @param string $updated
     *
     * @internal
     */
    public function setLastModifiedAt(?string $lastModifiedAt): void
    {
        $this->lastModifiedAt = $lastModifiedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * @param string $createdAt
     *
     * @internal
     */
    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getRatePlan(): ?RatePlanInterface
    {
        return $this->ratePlan;
    }

    /**
     * {@inheritdoc}
     */
    public function setRatePlan(?RatePlanInterface $ratePlan): void
    {
        $this->ratePlan = $ratePlan;
    }
}
