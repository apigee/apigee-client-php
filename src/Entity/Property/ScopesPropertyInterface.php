<?php

namespace Apigee\Edge\Entity\Property;

/**
 * Interface ScopesPropertyInterface.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface ScopesPropertyInterface
{
    /**
     * Get OAuth scopes.
     *
     * @return string[]
     */
    public function getScopes(): array;

    /**
     * Set OAuth scopes.
     *
     * @param string[] $scopes
     */
    public function setScopes(array $scopes): void;
}
