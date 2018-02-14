<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Entity\Property;

/**
 * Class StatusPropertyInterface.
 *
 * An entity type's controller should implements the StatusAwareEntityControllerInterface interface that extends this
 * interface.
 * It is also recommenced to add possible status values to the entity type as constants,
 * ex.: const STATUS_ACTIVE = 'active' to make SDK consumers' life easier.
 *
 * @see \Apigee\Edge\Api\Management\Controller\DeveloperController
 * @see \Apigee\Edge\Controller\StatusAwareEntityControllerInterface
 */
interface StatusPropertyInterface
{
    /**
     * @return null|string Status of the entity.
     */
    public function getStatus(): ?string;
}
