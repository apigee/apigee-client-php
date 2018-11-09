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

use Apigee\Edge\Api\Monetization\Entity\Property\IdPropertyAwareTrait;
use Apigee\Edge\Entity\Entity as BaseEntity;

/**
 * Base entity class for Monetization entities.
 */
abstract class Entity extends BaseEntity implements EntityInterface
{
    use IdPropertyAwareTrait;

    /**
     * @inheritdoc
     */
    public static function idProperty(): string
    {
        // All Monetization entities have an "id" property (besides name) and
        // even if both are unique and unchangeable we should use the "id".
        return 'id';
    }
}
