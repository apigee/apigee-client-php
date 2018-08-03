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

/**
 * Trait StatusPropertyAwareTrait.
 *
 *
 * @see StatusPropertyInterface
 */
trait StatusPropertyAwareTrait
{
    /** @var string */
    protected $status;

    /**
     * @inheritdoc
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Set status of this entity from an API response.
     *
     * The status of an entity can not be changed by modifying the value of this property. Read more about this in
     * the header of the StatusPropertyInterface.
     *
     * @param string $status
     *   Status of the entity.
     *
     * @see \Apigee\Edge\Entity\Property\StatusPropertyInterface
     *
     * @internal
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}
