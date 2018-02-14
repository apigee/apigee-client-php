<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Entity\Property;

use Apigee\Edge\Structure\AttributesProperty;

/**
 * Trait AttributesPropertyAwareTrait.
 *
 *
 * @see AttributesPropertyInterface
 */
trait AttributesPropertyAwareTrait
{
    /** @var \Apigee\Edge\Structure\AttributesProperty */
    protected $attributes;

    /**
     * @inheritdoc
     */
    public function getAttributes(): AttributesProperty
    {
        return $this->attributes;
    }

    /**
     * @inheritdoc
     */
    public function setAttributes(AttributesProperty $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * @inheritdoc
     */
    public function getAttributeValue(string $attribute): ?string
    {
        return $this->attributes->getValue($attribute);
    }

    /**
     * @inheritdoc
     */
    public function setAttribute(string $name, string $value): void
    {
        $this->attributes->add($name, $value);
    }

    /**
     * @inheritdoc
     */
    public function hasAttribute(string $name): bool
    {
        return $this->attributes->has($name);
    }

    /**
     * @inheritdoc
     */
    public function deleteAttribute(string $name): void
    {
        $this->attributes->delete($name);
    }
}
