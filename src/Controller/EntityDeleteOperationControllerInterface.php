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

namespace Apigee\Edge\Controller;

use Apigee\Edge\Entity\EntityInterface;

interface EntityDeleteOperationControllerInterface
{
    /**
     * Removes an entity from Apigee Edge.
     *
     * @param string $entityId
     *   ID of the entity to be deleted.
     *
     * @return EntityInterface
     *   The deleted entity.
     */
    public function delete(string $entityId): EntityInterface;
}
