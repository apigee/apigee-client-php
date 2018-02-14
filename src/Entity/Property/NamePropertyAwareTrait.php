<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
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
     * @inheritdoc
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the name of this entity from an Edge API response.
     *
     * The name of an entity can not be changed by modifying the value of this property because it is a primary key
     * when it is available.
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
