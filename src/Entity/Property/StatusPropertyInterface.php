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
