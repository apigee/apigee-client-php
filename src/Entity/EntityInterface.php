<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Entity;

/**
 * Interface EntityInterface.
 *
 * Describes public behaviour of all Apigee Edge entity.
 */
interface EntityInterface
{
    /**
     * Returns the primary id of an entity.
     *
     * @return string
     */
    public function idProperty(): string;

    /**
     * Returns the value of the primary id of an entity.
     *
     * @return null|string
     */
    public function id(): ?string;
}
