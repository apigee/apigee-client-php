<?php

/**
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

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\CommonEntityPropertiesInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Entity\Property\DisplayNamePropertyInterface;
use Apigee\Edge\Entity\Property\EnvironmentsPropertyInterface;
use Apigee\Edge\Entity\Property\NamePropertyInterface;
use Apigee\Edge\Entity\Property\PropertiesPropertyInterface;

/**
 * Interface OrganizationInterface.
 */
interface OrganizationInterface extends
    EntityInterface,
    CommonEntityPropertiesInterface,
    DisplayNamePropertyInterface,
    EnvironmentsPropertyInterface,
    NamePropertyInterface,
    PropertiesPropertyInterface
{
    /**
     * @return null|string
     */
    public function getType(): ?string;

    /**
     * @param string $type
     *
     * @throws \InvalidArgumentException
     */
    public function setType(string $type): void;

    /**
     * Returns possible values of "type" property.
     *
     * @return array
     */
    public function getTypes(): array;

    /**
     * Indicates whether the Content Persistance Service is enabled on the organization.
     *
     * @see https://community.apigee.com/questions/49310/how-many-custom-attributes-are-allowed-on-an-edge.html
     *
     * @return bool
     */
    public function isCpsEnabled(): bool;
}
