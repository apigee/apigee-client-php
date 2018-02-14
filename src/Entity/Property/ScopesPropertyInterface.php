<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Entity\Property;

/**
 * Interface ScopesPropertyInterface.
 */
interface ScopesPropertyInterface
{
    /**
     * Get OAuth scopes.
     *
     * @return string[]
     */
    public function getScopes(): array;

    /**
     * Set OAuth scopes.
     *
     * @param string[] $scopes
     */
    public function setScopes(array $scopes): void;
}
