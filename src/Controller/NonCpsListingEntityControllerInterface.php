<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Controller;

/**
 * Interface NonCpsListingEntityControllerInterface.
 *
 * For entities that does not support CPS limits in their listing API calls, ex.: organization, api product.
 *
 * @see https://docs.apigee.com/management/apis/get/organizations
 * @see https://docs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/apiproducts-0
 */
interface NonCpsListingEntityControllerInterface
{
    /**
     * Returns list of entities from Edge. The returned number of entities can _not_ be limited.
     *
     * @return EntityCrudOperationsControllerInterface[]
     *
     * @see \Apigee\Edge\Controller\EntityController::getEntities()
     */
    public function getEntities(): array;

    /**
     * Returns list of entity ids from Edge. The returned number of entities can _not_ be limited.
     *
     * @return array
     *
     * @see \Apigee\Edge\Controller\EntityController::getEntityIds()
     */
    public function getEntityIds(): array;
}
