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
interface EntityInterface extends \JsonSerializable
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
     * Creates an entity.
     *
     * @param array $values
     *   Associative array with entity properties and their values.
     * @return EntityInterface
     */
    public static function create(array $values): EntityInterface;

    /**
     * Ensure that it is implemented on all descendants.
     * @return string
     */
    public function __toString();
}
