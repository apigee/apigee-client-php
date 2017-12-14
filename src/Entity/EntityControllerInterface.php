<?php

namespace Apigee\Edge\Entity;

/**
 * Interface EntityControllerInterface.
 *
 * Describes public behavior of entity controllers that works with entities that belongs to an organization on Edge.
 * (99% of entities belongs to an organization on Edge, except the organization entity itself.)
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface EntityControllerInterface
{
    /**
     * @return string The name of the organization.
     */
    public function getOrganisation(): string;

    /**
     * @param string $orgName The name of the organization that the entity belongs.
     *
     * @return string
     *   The organization name.
     */
    public function setOrganisation(string $orgName): string;
}
