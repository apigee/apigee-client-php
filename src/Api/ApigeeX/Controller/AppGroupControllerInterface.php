<?php

/*
 * Copyright 2023 Google LLC
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

namespace Apigee\Edge\Api\ApigeeX\Controller;

use Apigee\Edge\Api\ApigeeX\Controller\PaginatedEntityIdListingControllerInterface;
use Apigee\Edge\Api\ApigeeX\Controller\PaginatedEntityListingControllerInterface;
use Apigee\Edge\Api\Management\Controller\AttributesAwareEntityControllerInterface;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Controller\EntityCrudOperationsControllerInterface;
use Apigee\Edge\Controller\StatusAwareEntityControllerInterface;

/**
 * Interface AppGroupControllerInterface.
 *
 * @see https://cloud.google.com/apigee/docs/reference/apis/apigee/rest/v1/organizations.appgroups
 */
interface AppGroupControllerInterface extends
    AttributesAwareEntityControllerInterface,
    EntityControllerInterface,
    EntityCrudOperationsControllerInterface,
    PaginatedEntityListingControllerInterface,
    PaginatedEntityIdListingControllerInterface,
    StatusAwareEntityControllerInterface
{
}
