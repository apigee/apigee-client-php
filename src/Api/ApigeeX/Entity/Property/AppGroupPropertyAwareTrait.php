<?php

/*
 * Copyright 2025 Google LLC
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

namespace Apigee\Edge\Api\ApigeeX\Entity\Property;

use Apigee\Edge\Api\ApigeeX\Entity\AppGroupInterface;

/**
 * Trait AppGroupPropertyAwareTrait.
 *
 * @see AppGroupPropertyInterface
 */
trait AppGroupPropertyAwareTrait
{
    /**
     * Value of "developer" from the API response.
     *
     * @var AppGroupInterface|null
     */
    protected $appgroup;

    /**
     * {@inheritdoc}
     */
    public function getAppGroup(): ?AppGroupInterface
    {
        return $this->appgroup;
    }

    /**
     * {@inheritdoc}
     *
     * @internal
     */
    public function setAppGroup(AppGroupInterface $appgroup): void
    {
        $this->appgroup = $appgroup;
    }
}
