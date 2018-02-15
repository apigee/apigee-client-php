<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Api\Management\Entity\DeveloperInterface;
use Apigee\Edge\Controller\CpsLimitEntityControllerInterface;
use Apigee\Edge\Controller\CpsListingEntityControllerInterface;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Controller\EntityCrudOperationsControllerInterface;
use Apigee\Edge\Controller\StatusAwareEntityControllerInterface;

/**
 * Describes methods available on developers endpoint.
 *
 * @see https://docs.apigee.com/api/developers-0
 */
interface DeveloperControllerInterface extends
    AttributesAwareEntityControllerInterface,
    CpsLimitEntityControllerInterface,
    CpsListingEntityControllerInterface,
    EntityControllerInterface,
    EntityCrudOperationsControllerInterface,
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
