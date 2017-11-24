<?php

namespace Apigee\Edge\Entity\Property;

/**
 * Trait DeveloperIdPropertyAwareTrait.
 *
 * @package Apigee\Edge\Entity\Property
 * @author Dezső Biczó <mxr576@gmail.com>
 * @see \Apigee\Edge\Entity\Property\DeveloperIdPropertyInterface
 */
trait DeveloperIdPropertyAwareTrait
{
    /** @var string UUID of the developer entity. */
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
