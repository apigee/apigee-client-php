<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Controller;

use Apigee\Edge\Structure\CpsListLimitInterface;

/**
 * Interface CpsLimitEntityControllerInterface.
 *
 * For entities that supports CPS limit in their listing API calls, ex.: developer.
 *
 * @see https://docs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/developers
 */
interface CpsLimitEntityControllerInterface
{
    /**
     * Returns a representation of a Core Persistence Services (CPS) limit.
     *
     * This limit can be used list API calls on Edge to limit the number of returned
     * results but CPS is not enabled on all organisations.
     *
     * @param string $startKey
     *    The primary key of the entity that the list should start.
     * @param int $limit
     *    Number of entities to return.
     *
     * @return CpsListLimitInterface
     *
     * @see https://docs.apigee.com/api-services/content/api-reference-getting-started#cps
     */
    public function createCpsLimit(string $startKey, int $limit): CpsListLimitInterface;
}
