<?php

namespace Apigee\Edge;

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
