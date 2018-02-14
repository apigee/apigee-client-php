<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
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
