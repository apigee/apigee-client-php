<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Controller;

use Apigee\Edge\Structure\CpsListLimitInterface;

/**
 * Interface CpsListingEntityControllerInterface.
 */
interface CpsListingEntityControllerInterface
{
    /**
     * Returns list of entities from Edge. The returned number of entities can be limited.
     *
     * @param \Apigee\Edge\Structure\CpsListLimitInterface|null $cpsLimit
     *
     * @return array
     */
    public function getEntities(CpsListLimitInterface $cpsLimit = null): array;

    /**
     * Returns list of entity ids from Edge. The returned number of entities can be limited.
     *
     * @param \Apigee\Edge\Structure\CpsListLimitInterface|null $cpsLimit
     *
     * @return \Apigee\Edge\Entity\EntityInterface[]
     */
    public function getEntityIds(CpsListLimitInterface $cpsLimit = null): array;
}
