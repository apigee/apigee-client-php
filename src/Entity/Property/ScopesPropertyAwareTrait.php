<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Entity\Property;

/**
 * Trait ScopesPropertyAwareTrait.
 *
 *
 * @see \Apigee\Edge\Entity\Property\ScopesPropertyInterface
 */
trait ScopesPropertyAwareTrait
{
    /** @var string[] OAuth scopes. */
    protected $scopes = [];

    /**
     * @inheritdoc
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * @inheritdoc
     */
    public function setScopes(array $scopes): void
    {
        $this->scopes = $scopes;
    }
}
