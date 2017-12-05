<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Entity\EntityControllerInterface;
use Apigee\Edge\Entity\EntityCrudOperationsControllerInterface;
use Apigee\Edge\Entity\NonCpsLimitEntityControllerInterface;

/**
 * Interface ApiProductControllerInterface.
 *
 * @package Apigee\Edge\Api\Management\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 * @link https://docs.apigee.com/api/api-products-1
 */
interface ApiProductControllerInterface extends
    AttributesAwareEntityControllerInterface,
    EntityControllerInterface,
    EntityCrudOperationsControllerInterface,
    NonCpsLimitEntityControllerInterface
{

    /**
     * Search API products by their attributes.
     *
     * @param string $attributeName
     * @param string $attributeValue
     *
     * @return array
     *   Array of API product names.
     *
     * @link https://docs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/apiproducts
     */
    public function searchByAttribute(string $attributeName, string $attributeValue): array;
}
