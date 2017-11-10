<?php

namespace Apigee\Edge\Entity\Property;

/**
 * Trait OrganizationNamePropertyAwareTrait.
 *
 * @package Apigee\Edge\Entity\Property
 * @author Dezső Biczó <mxr576@gmail.com>
 * @see OrganizationNamePropertyInterface
 */
trait OrganizationNamePropertyAwareTrait
{
    /** @var string Name of the organization that this entity belongs to. */
    protected $organizationName = '';

    /**
     * @return string Name of the organization that this entity belongs to.
     */
    public function getOrganizationName(): string
    {
        return $this->organizationName;
    }

    /**
     * @param string $orgName Name of the organization that this entity belongs to.
     */
    public function setOrganizationName(string $orgName): void
    {
        $this->organizationName = $orgName;
    }
}
