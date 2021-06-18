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

namespace Apigee\Edge\Api\Monetization\Entity\Property;

/**
 * Trait KeepOriginalStartDatePropertyAwareTrait.
 *
 * @see \Apigee\Edge\Api\Monetization\Entity\Property\KeepOriginalStartDatePropertyInterface
 */
trait KeepOriginalStartDatePropertyAwareTrait
{
    /**
     * @var bool|null
     *
     * @see https://github.com/apigee/apigee-client-php/issues/47
     */
    protected $keepOriginalStartDate;

    /**
     * {@inheritdoc}
     */
    public function isKeepOriginalStartDate(): bool
    {
        return (bool) $this->keepOriginalStartDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setKeepOriginalStartDate(bool $keepOriginalStartDate): void
    {
        $this->keepOriginalStartDate = $keepOriginalStartDate;
    }

    /**
     * {@inheritdoc}
     */
    public function getKeepOriginalStartDate(): ?bool
    {
        return $this->keepOriginalStartDate;
    }
}
