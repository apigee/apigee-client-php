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

use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Controller\EntityCrudOperationsControllerInterface;
use Apigee\Edge\Controller\NonPaginatedEntityIdListingControllerInterface;

/**
 * Interface EnvironmentControllerInterface.
 *
 * @see https://apidocs.apigee.com/api/environments
 *
 * @TODO
 *
 * @see https://apidocs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/environments/%7Benv_name%7D/deployments-0
 * @see https://apidocs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/environments/%7Benv_name%7D/servers
 * @see https://apidocs.apigee.com/management/apis/delete/organizations/%7Borg_name%7D/environments/%7Benv_name%7D/servers
 * @see https://apidocs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/environments/%7Benv_name%7D/servers
 */
interface EnvironmentControllerInterface extends
    EntityControllerInterface,
    EntityCrudOperationsControllerInterface,
    NonPaginatedEntityIdListingControllerInterface
{
}
