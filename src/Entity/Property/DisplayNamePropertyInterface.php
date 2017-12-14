<?php

namespace Apigee\Edge\Entity\Property;

/**
 * Interface DisplayNamePropertyInterface.
 *
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
