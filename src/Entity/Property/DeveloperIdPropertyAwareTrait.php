<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Entity\Property;

/**
 * Trait DeveloperIdPropertyAwareTrait.
 *
 *
 * @see \Apigee\Edge\Entity\Property\DeveloperIdPropertyInterface
 */
trait DeveloperIdPropertyAwareTrait
{
    /** @var string|null UUID of the developer entity. */
    protected $developerId;

    /**
     * @inheritdoc
     */
    public function getDeveloperId(): ?string
    {
        return $this->developerId;
    }

    /**
     * Set developer id from an Edge API response.
     *
     * @param string $developerId UUID.
     *
     * @internal
     */
    public function setDeveloperId(string $developerId): void
    {
        $this->developerId = $developerId;
    }
}
