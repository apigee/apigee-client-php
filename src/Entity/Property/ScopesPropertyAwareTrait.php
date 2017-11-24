<?php

namespace Apigee\Edge\Entity\Property;

/**
 * Trait ScopesPropertyAwareTrait.
 *
 * @package Apigee\Edge\Entity\Property
 * @author Dezső Biczó <mxr576@gmail.com>
 * @see \Apigee\Edge\Entity\Property\ScopesPropertyInterface
 */
trait ScopesPropertyAwareTrait
{
    /** @var string[] OAuth scopes. */
    protected $scopes = [];

    /**
     * @inheritdoc
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * @inheritdoc
     */
    public function setScopes(array $scopes): void
    {
        $this->scopes = $scopes;
    }
}
