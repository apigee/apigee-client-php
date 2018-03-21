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

namespace Apigee\Edge\Entity\Property;

use Apigee\Edge\Structure\PropertiesProperty;

/**
 * Interface PropertiesPropertyInterface.
 */
interface PropertiesPropertyInterface
{
    /**
     * @return array
     */
    public function getProperties(): array;

    /**
     * @param PropertiesProperty $properties
     */
    public function setProperties(PropertiesProperty $properties): void;

    /**
     * @param string $property
     *
     * @return null|string
     */
    public function getPropertyValue(string $property): ?string;

    /**
     * @param string $name
     * @param string $value
     */
    public function addProperty(string $name, string $value): void;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasProperty(string $name): bool;

    /**
     * @param string $name
     */
    public function deleteProperty(string $name): void;
}
