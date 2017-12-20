<?php

namespace Apigee\Edge\Entity;

/**
 * Interface EntityInterface.
 *
 * Describes public behaviour of all Apigee Edge entity.
 *
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
     * Returns the value of the primary id of an entity.
     *
     * @return null|string
     */
    public function id(): ?string;
}
