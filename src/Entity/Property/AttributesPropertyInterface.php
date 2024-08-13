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

use Apigee\Edge\Structure\AttributesProperty;

/**
 * Class AttributesAttributeInterface.
 *
 * @see AttributesPropertyAwareTrait
 */
interface AttributesPropertyInterface
{
    /**
     * @return AttributesProperty
     */
    public function getAttributes(): ?AttributesProperty;

    /**
     * @param AttributesProperty $attributes
     */
    public function setAttributes(AttributesProperty $attributes): void;

    /**
     * @param string $attribute
     *
     * @return string|null
     */
    public function getAttributeValue(string $attribute): ?string;

    /**
     * @param string $name
     * @param string $value
     */
    public function setAttribute(string $name, string $value): void;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasAttribute(string $name): bool;

    /**
     * @param string $name
     */
    public function deleteAttribute(string $name): void;
}
