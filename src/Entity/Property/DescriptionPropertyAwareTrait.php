<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Entity\Property;

/**
 * Trait DescriptionPropertyAwareTrait.
 *
 *
 * @see \Apigee\Edge\Entity\Property\DescriptionPropertyInterface
 */
trait DescriptionPropertyAwareTrait
{
    /** @var string */
    protected $description = '';

    /**
     * @inheritdoc
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @inheritdoc
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
