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

use DateTimeImmutable;

/**
 * Interface CommonEntityPropertiesInterface.
 *
 * Accessor methods for common Apigee Edge entities.
 *
 * @see CommonEntityPropertiesAwareTrait
 */
interface CommonEntityPropertiesInterface
{
    /**
     * Returns creation date of entity.
     *
     * @return DateTimeImmutable|null
     */
    public function getCreatedAt(): ?DateTimeImmutable;

    /**
     * Returns the email address of the user/developer who created the entity.
     *
     * @deprecated in 2.0.4, will be removed before 3.0.0. Unsupported in Hybrid.
     * @see https://github.com/apigee/apigee-client-php/issues/65
     *
     * @return string|null Email address.
     */
    public function getCreatedBy(): ?string;

    /**
     * Returns last modification date of entity.
     *
     * @return DateTimeImmutable|null
     */
    public function getLastModifiedAt(): ?DateTimeImmutable;

    /**
     * Returns the email address of the user/developer who modified the entity the last time.
     *
     * @deprecated in 2.0.4, will be removed before 3.0.0. Unsupported in Hybrid.
     * @see https://github.com/apigee/apigee-client-php/issues/65
     *
     * @return string|null Email address.
     */
    public function getLastModifiedBy(): ?string;
}
