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

namespace Apigee\Edge\Api\Monetization\Controller;

use Apigee\Edge\Controller\EntityControllerInterface;

/**
 * Interface TermsAndConditionsControllerInterface.
 *
 * @see https://apidocs.apigee.com/monetize/apis/
 * @see https://docs.apigee.com/api-platform/monetization/specify-terms-and-conditions
 */
interface TermsAndConditionsControllerInterface extends EntityControllerInterface,
    EntityCrudOperationsControllerInterface
{
    /**
     * Loads all terms and conditions.
     *
     * @param bool $currentOnly
     *   Get current terms and conditions only. Defaults to FALSE.
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\TermsAndConditionsInterface[]
     */
    public function getEntities(bool $currentOnly = false): array;

    /**
     * Loads all terms and conditions in the provided range.
     *
     * @param int|null $limit
     *   Number of entities to load maximum. Null falls back to the default
     *   of the endpoint, ex.: 20.
     * @param int $page
     *   Number of page that you want to return.
     * @param bool $currentOnly
     *   Get current terms and conditions only. Defaults to FALSE.
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\TermsAndConditionsInterface[]
     */
    public function getPaginatedEntityList(int $limit = null, int $page = 1, bool $currentOnly = false): array;
}
