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
 * Trait NamePropertyAwareTrait.
 */
trait NamePropertyAwareTrait
{
    /** @var string */
    protected $name;

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the name of this entity from an Apigee Edge API response.
     *
     * The name of an entity can not be changed by modifying the value of this
     * property because it is a primary key when it is available.
     *
     * @param string $name
     *   Name of the entity.
     *
     * @internal
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
