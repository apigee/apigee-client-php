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

namespace Apigee\Edge\Entity;

use Apigee\Edge\Api\Management\Entity\Organization;
use Apigee\Edge\Structure\BaseObject;

/**
 * Base representation of an Edge entity.
 *
 * Common properties and methods that are available on all Apigee Edge entity.
 *
 * Rules:
 * - Name of an entity property should be the same as the one in the Edge response.
 * - Entity properties should not be public, but they should have public getters and setters.
 *   (Public setters and getters are required by symfony/serializer implementations, ex.: ObjectNormalizer,
 *   EntityNormalizer, etc.)
 * - An entity should not have other properties than what Edge returns for a related API call, but it could have
 *   additional helper methods that make developers life easier. @see Organization::isCpsEnabled()
 * - Entity properties with object or array types must be initialized.
 */
abstract class Entity extends BaseObject implements EntityInterface
{
    /**
     * On the majority of entities this property is the primary entity.
     */
    private const DEFAULT_ID_FIELD = 'name';

    /**
     * @inheritdoc
     */
    public function id(): ?string
    {
        return $this->{$this->idProperty()};
    }

    /**
     * @inheritdoc
     */
    public function idProperty(): string
    {
        return self::DEFAULT_ID_FIELD;
    }
}
