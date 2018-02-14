<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
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
