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

interface FreemiumPropertiesInterface
{
    public const FREEMIUM_DURATION_DAY = 'DAY';

    public const FREEMIUM_DURATION_WEEK = 'WEEK';

    public const FREEMIUM_DURATION_MONTH = 'MONTH';

    public const FREEMIUM_DURATION_QUARTER = 'QUARTER';

    /**
     * @return int|null
     */
    public function getFreemiumDuration(): ?int;

    /**
     * @param int $freemiumDuration
     */
    public function setFreemiumDuration(int $freemiumDuration): void;

    /**
     * @return null|string
     */
    public function getFreemiumDurationType(): ?string;

    /**
     * @param string $freemiumDurationType
     */
    public function setFreemiumDurationType(string $freemiumDurationType): void;

    /**
     * @return int|null
     */
    public function getFreemiumUnit(): ?int;

    /**
     * @param int $freemiumUnit
     */
    public function setFreemiumUnit(int $freemiumUnit): void;
}
