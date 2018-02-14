<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Entity\Property;

/**
 * Trait DisplayNamePropertyAwareTrait.
 */
trait DisplayNamePropertyAwareTrait
{
    /** @var null|string */
    protected $displayName;

    /**
     * @inheritdoc
     */
    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    /**
     * @inheritdoc
     */
    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }
}
