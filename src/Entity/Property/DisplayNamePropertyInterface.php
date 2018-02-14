<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Entity\Property;

/**
 * Interface DisplayNamePropertyInterface.
 */
interface DisplayNamePropertyInterface
{
    /**
     * @return null|string
     */
    public function getDisplayName(): ?string;

    /**
     * @param string $displayName
     */
    public function setDisplayName(string $displayName): void;
}
