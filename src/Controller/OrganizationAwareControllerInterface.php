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
 * Interface OrganizationAwareControllerInterface.
 *
 * Describes public behavior of controllers that communicates with API endpoints that belongs to an organization on
 * Apigee Edge. (99% of Apigee Edge entities belongs to an organization, except the organization entity itself.)
 */
interface OrganizationAwareControllerInterface
{
    /**
     * @return string The name of the organization.
     */
    public function getOrganisation(): string;
}
