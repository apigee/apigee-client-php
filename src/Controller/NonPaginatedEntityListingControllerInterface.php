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

/**
 * Interface NonPaginatedEntityListingControllerInterface.
 *
 * For those entities that can be listed as objects or by their entity ids but
 * pagination is supported on their endpoint. Ex.: organizations.
 *
 * @see https://docs.apigee.com/management/apis/get/organizations
 */
interface NonPaginatedEntityListingControllerInterface
{
    /**
     * Returns list of entities from Edge.
     *
     * The returned number of entities can _not_ be limited.
     *
     * @return \Apigee\Edge\Entity\EntityInterface[]
     */
    public function getEntities(): array;
}
