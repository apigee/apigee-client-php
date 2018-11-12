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

use Apigee\Edge\Api\Monetization\Entity\Property\StartDatePropertyInterface;
use Apigee\Edge\Entity\Property\DescriptionPropertyInterface;

interface TermsAndConditionsInterface extends
    OrganizationAwareEntityInterface,
    DescriptionPropertyInterface,
    StartDatePropertyInterface
{
    /**
     * @return null|string
     */
    public function getUrl(): ?string;

    /**
     * @param null|string $url
     */
    public function setUrl(?string $url): void;

    /**
     * @return string
     */
    public function getVersion(): string;

    /**
     * @param string $version
     */
    public function setVersion(string $version): void;
}
