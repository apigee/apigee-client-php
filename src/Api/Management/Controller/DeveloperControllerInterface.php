<?php

/*
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

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Api\Management\Entity\DeveloperInterface;
use Apigee\Edge\Controller\CpsLimitEntityControllerInterface;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Controller\EntityCrudOperationsControllerInterface;
use Apigee\Edge\Controller\PaginationEntityListingControllerInterface;
use Apigee\Edge\Controller\StatusAwareEntityControllerInterface;

/**
 * Describes methods available on developers endpoint.
 *
 * @see https://docs.apigee.com/api/developers-0
 */
interface DeveloperControllerInterface extends
    AttributesAwareEntityControllerInterface,
    CpsLimitEntityControllerInterface,
    EntityControllerInterface,
    EntityCrudOperationsControllerInterface,
    PaginationEntityListingControllerInterface,
    StatusAwareEntityControllerInterface
{
    /**
     * Get developer entity by app.
     *
     * @param string $appName
     *
     * @throws \Apigee\Edge\Api\Management\Exception\DeveloperNotFoundException
     *
     * @return DeveloperInterface
     *
     * @see https://docs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/developers-0
     */
    public function getDeveloperByApp(string $appName): DeveloperInterface;
}
