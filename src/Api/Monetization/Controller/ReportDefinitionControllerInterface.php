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

use Apigee\Edge\Api\Monetization\Structure\Reports\Criteria\AbstractCriteria;
use Apigee\Edge\Controller\EntityControllerInterface;

/**
 * Interface ReportDefinitionControllerInterface.
 *
 * @see https://apidocs.apigee.com/monetize/apis/
 * @see https://docs.apigee.com/api-platform/monetization/create-reports#api
 */
interface ReportDefinitionControllerInterface extends EntityControllerInterface, EntityCrudOperationsControllerInterface, EntityListingControllerInterface
{
    /**
     * Get filtered list of entities.
     *
     * @param int|null $limit
     *   Number of entities to load maximum. Null falls back to the default
     *   of the endpoint, ex.: 20.
     * @param int $page
     *   Number of page that you want to return.
     * @param string|null $sort
     *   Field by which to sort entities. Null falls back to the default of the
     *   endpoint, ex.: UPDATED:DESC.
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\ReportDefinitionInterface[]
     */
    public function getFilteredEntities(?int $limit = null, int $page = 1, ?string $sort = null): array;

    /**
     * Generate a report.
     *
     * @param AbstractCriteria $criteria
     *   Search criteria for the report.
     *
     * @return string
     *   The raw API response body as a string. The response body contains a
     *   CSV, but the structure of the CSV can vary based on the criteria.
     */
    public function generateReport(AbstractCriteria $criteria): string;
}
