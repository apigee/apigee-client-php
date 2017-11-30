<?php

namespace Apigee\Edge\Entity;

/**
 * Interface EntityInterface.
 *
 * Describes public behaviour of all Apigee Edge entity.
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface EntityInterface
{
    /**
     * Returns the primary id of an entity.
     *
     * @return string
     */
    public function idProperty(): string;

    /**
     * Returns the name of the property that contains primary id of an entity.
     *
     * @return string
     */
    public function id(): ?string;
}
