<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Controller;

/**
 * Interface OrganizationAwareControllerInterface.
 *
 * Describes public behavior of controllers that communicates with API endpoints that belongs to an organization on Edge.
 * (This is especially true for 99% of Edge entities, except the organization entity.)
 */
interface OrganizationAwareControllerInterface
{
    /**
     * @return string The name of the organization.
     */
    public function getOrganisation(): string;

    /**
     * @param string $orgName The name of the organization that the entity belongs.
     */
    public function setOrganisation(string $orgName): void;
}
