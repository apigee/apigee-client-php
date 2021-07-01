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
 * Trait FreemiumDurationPropertiesAwareTrait.
 *
 * @see \Apigee\Edge\Api\Monetization\Entity\Property\FreemiumPropertiesInterface
 */
trait FreemiumPropertiesAwareTrait
{
    /** @var int|null */
    protected $freemiumDuration;

    /** @var string|null */
    protected $freemiumDurationType;

    /** @var int|null */
    protected $freemiumUnit;

    /**
     * {@inheritdoc}
     */
    public function getFreemiumDuration(): ?int
    {
        return $this->freemiumDuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setFreemiumDuration(int $freemiumDuration): void
    {
        $this->freemiumDuration = $freemiumDuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getFreemiumDurationType(): ?string
    {
        return $this->freemiumDurationType;
    }

    /**
     * {@inheritdoc}
     */
    public function setFreemiumDurationType(string $freemiumDurationType): void
    {
        $this->freemiumDurationType = $freemiumDurationType;
    }

    /**
     * {@inheritdoc}
     */
    public function getFreemiumUnit(): ?int
    {
        return $this->freemiumUnit;
    }

    /**
     * {@inheritdoc}
     */
    public function setFreemiumUnit(int $freemiumUnit): void
    {
        $this->freemiumUnit = $freemiumUnit;
    }
}
