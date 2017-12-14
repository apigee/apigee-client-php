<?php

namespace Apigee\Edge\Entity\Property;

/**
 * Interface OrganizationNamePropertyInterface.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
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
