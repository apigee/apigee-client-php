<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Entity\Property;

/**
 * Trait OrganizationNamePropertyAwareTrait.
 *
 *
 * @see OrganizationNamePropertyInterface
 */
trait OrganizationNamePropertyAwareTrait
{
    /** @var string Name of the organization that this entity belongs to. */
    protected $organizationName = '';

    /**
     * @inheritdoc
     */
    public function getOrganizationName(): ?string
    {
        return $this->organizationName;
    }

    /**
     * @inheritdoc
     */
    public function setOrganizationName(string $orgName): void
    {
        $this->organizationName = $orgName;
    }
}
