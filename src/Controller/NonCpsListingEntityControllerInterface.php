<?php

/**
 * Copyright 2018 Google LLC
 *                            
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *                            
 *      https://www.apache.org/licenses/LICENSE-2.0
 *                            
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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
