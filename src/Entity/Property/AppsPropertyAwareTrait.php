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
 * Trait AppsPropertyAwareTrait.
 *
 * @see \Apigee\Edge\Entity\Property\AppsPropertyInterface
 */
trait AppsPropertyAwareTrait
{
    /** @var string[] */
    protected $apps = [];

    /**
     * @inheritdoc
     */
    public function getApps(): array
    {
        return $this->apps;
    }

    /**
     * @inheritdoc
     */
    public function hasApp(string $appName): bool
    {
        return in_array($appName, $this->apps);
    }

    /**
     * Set app names from an Edge API response.
     *
     * Apps of a developer can not be changed by modifying this property's value.
     *
     * @param string[] $apps
     *
     * @internal
     */
    public function setApps(array $apps): void
    {
        $this->apps = $apps;
    }
}
