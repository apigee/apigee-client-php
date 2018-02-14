<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Entity;

/**
 * Interface CommonEntityPropertiesInterface.
 *
 * Accessor methods for common Apigee Edge entities.
 *
 *
 * @see CommonEntityPropertiesAwareTrait
 */
interface CommonEntityPropertiesInterface
{
    /**
     * Returns creation date of entity.
     *
     * @return null|\DateTimeImmutable
     */
    public function getCreatedAt(): ?\DateTimeImmutable;

    /**
     * Returns the email address of the user/developer who created the entity.
     *
     * @return null|string Email address.
     */
    public function getCreatedBy(): ?string;

    /**
     * Returns last modification date of entity.
     *
     * @return null|\DateTimeImmutable
     */
    public function getLastModifiedAt(): ?\DateTimeImmutable;

    /**
     * Returns the email address of the user/developer who modified the entity the last time.
     *
     * @return null|string Email address.
     */
    public function getLastModifiedBy(): ?string;
}
