<?php

namespace Apigee\Edge\Entity\Property;

/**
 * Interface DisplayNamePropertyInterface.
 *
 * @package Apigee\Edge\Entity\Property
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface DisplayNamePropertyInterface
{
    /**
     * @return null|string
     */
    public function getDisplayName(): ?string;

    /**
     * @param string $displayName
     */
    public function setDisplayName(string $displayName): void;
}
