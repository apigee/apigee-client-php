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

use Apigee\Edge\Api\Monetization\Entity\Property\OrganizationPropertyAwareTrait;
use Apigee\Edge\Api\Monetization\Structure\Reports\Criteria\AbstractCriteria;
use Apigee\Edge\Entity\Property\DescriptionPropertyAwareTrait;
use Apigee\Edge\Entity\Property\NamePropertyAwareTrait;

class ReportDefinition extends Entity implements ReportDefinitionInterface
{
    use NamePropertyAwareTrait;
    use DescriptionPropertyAwareTrait;
    use OrganizationPropertyAwareTrait;

    /** @var \Apigee\Edge\Api\Monetization\Structure\Reports\Criteria\AbstractCriteria */
    protected $criteria;

    /**
     * @var \DateTimeImmutable|null
     */
    protected $lastModified;

    /**
     * {@inheritdoc}
     */
    public function getCriteria(): AbstractCriteria
    {
        return $this->criteria;
    }

    /**
     * {@inheritdoc}
     */
    public function setCriteria(AbstractCriteria $criteria): void
    {
        $this->criteria = $criteria;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastModified(): ?\DateTimeImmutable
    {
        return $this->lastModified;
    }

    /**
     * {@inheritdoc}
     *
     * {@internal}
     */
    public function setLastModified(\DateTimeImmutable $lastModified): void
    {
        $this->lastModified = $lastModified;
    }
}
