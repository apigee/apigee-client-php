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

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Exception\ClientErrorException;
use Apigee\Edge\Structure\AttributesProperty;

/**
 * Interface AttributesAwareEntityControllerInterface.
 *
 * Contains reusable methods for those entities' controllers that supports attributes and attributes can be
 * modified on dedicated endpoints. Example: Developers, API products, etc.
 *
 * @see https://docs.apigee.com/api/developers-0
 * @see https://docs.apigee.com/api/api-products-1
 * @see AttributesAwareEntityControllerTrait
 */
interface AttributesAwareEntityControllerInterface
{
    /**
     * Returns a list of all entity attributes.
     *
     * @param string $entityId
     *
     * @return AttributesProperty
     */
    public function getAttributes(string $entityId): AttributesProperty;

    /**
     * Returns the value of an entity attribute.
     *
     * @param string $entityId
     * @param string $name
     *
     * @return string
     */
    public function getAttribute(string $entityId, string $name): string;

    /**
     * Updates or creates developer attributes.
     *
     * This API replaces the current list of attributes with the attributes specified in the request body.
     * This lets you update existing attributes, add new attributes, or delete existing attributes by omitting them
     * from the request body.
     *
     * @param string $entityId
     * @param AttributesProperty $attributes
     *
     * @return AttributesProperty
     */
    public function updateAttributes(string $entityId, AttributesProperty $attributes): AttributesProperty;

    /**
     * Updates the value of an entity attribute.
     *
     * @param string $entityId
     * @param string $name
     * @param string $value
     *
     * @throws ClientErrorException If attribute does not exist.
     *
     * @return string
     */
    public function updateAttribute(string $entityId, string $name, string $value): string;

    /**
     * Deletes an entity attribute.
     *
     * @param string $entityId
     * @param string $name
     *
     * @throws ClientErrorException If attribute does not exist.
     */
    public function deleteAttribute(string $entityId, string $name): void;
}
