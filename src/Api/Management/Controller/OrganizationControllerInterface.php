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

use Apigee\Edge\Controller\EntityCrudOperationsControllerInterface;
use Apigee\Edge\Controller\NonCpsListingEntityControllerInterface;

/**
 * Interface OrganizationControllerInterface.
 *
 * Describes methods available on organizations.
 *
 * @see https://docs.apigee.com/api/organizations-0
 *
 * TODO
 * @see https://docs.apigee.com/management/apis/put/organizations/%7Borg_name%7D
 * @see https://docs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/deployments-0
 * @see https://docs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/pods
 * @see https://docs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/pods
 * @see https://docs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/pods-0
 */
interface OrganizationControllerInterface extends
    EntityCrudOperationsControllerInterface,
    NonCpsListingEntityControllerInterface
{
}
