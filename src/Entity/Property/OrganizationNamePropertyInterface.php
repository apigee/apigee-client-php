<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Entity\Property;

/**
 * Interface OrganizationNamePropertyInterface.
 */
interface OrganizationNamePropertyInterface
{
    /**
     * @return null|string Name of the organization that this entity belongs to.
     */
    public function getOrganizationName(): ?string;

    /**
     * @param string $orgName Name of the organization that this entity belongs to.
     */
    public function setOrganizationName(string $orgName): void;
}
