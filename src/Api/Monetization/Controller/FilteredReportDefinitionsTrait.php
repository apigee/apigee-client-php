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

use Apigee\Edge\Controller\BaseEndpointAwareControllerTrait;

trait FilteredReportDefinitionsTrait
{
    use BaseEndpointAwareControllerTrait;
    use PaginatedListingHelperTrait;

    /**
     * {@inheritdoc}
     */
    public function getFilteredEntities(int $limit = null, int $page = 1, string $sort = null): array
    {
        $queryParams = [];
        if (null !== $sort) {
            $queryParams = ['sort' => $sort];
        }

        return $this->listEntitiesInRange($this->getBaseEndpointUri()->withQuery(http_build_query($queryParams)), $limit, $page);
    }
}
