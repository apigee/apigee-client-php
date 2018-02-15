<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Controller\EntityCrudOperationsControllerInterface;
use Apigee\Edge\Controller\NonCpsListingEntityControllerInterface;

/**
 * Interface ApiProductControllerInterface.
 *
 * @see https://docs.apigee.com/api/api-products-1
 */
interface ApiProductControllerInterface extends
    AttributesAwareEntityControllerInterface,
    EntityControllerInterface,
    EntityCrudOperationsControllerInterface,
    NonCpsListingEntityControllerInterface
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
     * @see https://docs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/apiproducts
     */
    public function searchByAttribute(string $attributeName, string $attributeValue): array;
}
