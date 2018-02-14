<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Entity\Property;

use Apigee\Edge\Structure\PropertiesProperty;

/**
 * Trait PropertiesPropertyAwareTrait.
 */
trait PropertiesPropertyAwareTrait
{
    /** @var \Apigee\Edge\Structure\PropertiesProperty */
    protected $properties;

    /**
     * @inheritdoc
     */
    public function getProperties(): array
    {
        return $this->properties->values();
    }

    /**
     * @inheritdoc
     */
    public function setProperties(PropertiesProperty $properties): void
    {
        $this->properties = $properties;
    }

    /**
     * @inheritdoc
     */
    public function getPropertyValue(string $property): ?string
    {
        return $this->properties->getValue($property);
    }

    /**
     * @inheritdoc
     */
    public function addProperty(string $name, string $value): void
    {
        $this->properties->add($name, $value);
    }

    /**
     * @inheritdoc
     */
    public function hasProperty(string $name): bool
    {
        return $this->properties->has($name);
    }

    /**
     * @inheritdoc
     */
    public function deleteProperty(string $name): void
    {
        $this->properties->delete($name);
    }
}
