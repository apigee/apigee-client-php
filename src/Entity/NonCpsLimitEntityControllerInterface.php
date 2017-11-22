<?php

namespace Apigee\Edge\Entity;

/**
 * Interface NonCpsLimitEntityControllerInterface.
 *
 * For entities that does not support CPS limits in their listing API calls, ex.: organization, api product.
 * @link https://docs.apigee.com/management/apis/get/organizations
 * @link https://docs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/apiproducts-0
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface NonCpsLimitEntityControllerInterface extends BaseEntityControllerInterface
{

    /**
     * Returns list of entities from Edge. The returned number of entities can _not_ be limited.
     *
     * @return BaseEntityControllerInterface[]
     * @see \Apigee\Edge\Entity\EntityController::getEntities()
     */
    public function getEntities(): array;

    /**
     * Returns list of entity ids from Edge. The returned number of entities can _not_ be limited.
     *
     * @return array
     * @see \Apigee\Edge\Entity\EntityController::getEntityIds()
     */
    public function getEntityIds(): array;
}
