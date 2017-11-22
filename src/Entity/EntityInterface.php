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
    public function id(): string;

    /**
     * Returns creation date of entity.
     *
     * @return string Unix timestamp.
     */
    public function getCreatedAt(): string;

    /**
     * Returns the email address of the user/developer who created the entity.
     *
     * @return string Email address.
     */
    public function getCreatedBy(): string;

    /**
     * Returns last modification date of entity.
     *
     * @return string Unix timestamp.
     */
    public function getLastModifiedAt(): string;

    /**
     * Returns the email address of the user/developer who modified the entity the last time.
     *
     * @return string Email address.
     */
    public function getLastModifiedBy(): string;
}
