<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Controller;

/**
 * Trait OrganizationAwareControllerInterfaceTrait.
 */
trait OrganizationAwareControllerTrait
{
    /** @var string Name of the organization that the entity belongs to. */
    protected $organization;

    /**
     * @inheritdoc
     */
    public function getOrganisation(): string
    {
        return $this->organization;
    }

    /**
     * @inheritdoc
     */
    public function setOrganisation(string $orgName): void
    {
        $this->organization = $orgName;
    }
}
