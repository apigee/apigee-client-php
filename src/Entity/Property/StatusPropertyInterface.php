<?php

namespace Apigee\Edge\Entity\Property;

/**
 * Class StatusPropertyInterface.
 *
 * An entity type's controller should implements the StatusAwareEntityControllerInterface interface that extends this
 * interface.
 * It is also recommenced to add possible status values to the entity type as constants,
 * ex.: const STATUS_ACTIVE = 'active' to make SDK consumers' life easier.
 *
 * @see \Apigee\Edge\Api\Management\Controller\DeveloperController
 * @see \Apigee\Edge\Entity\StatusAwareEntityControllerInterface
 *
 * @package Apigee\Edge\Entity\Property
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface StatusPropertyInterface
{
    /**
     * @return string Status of the entity.
     */
    public function getStatus(): string;
}
