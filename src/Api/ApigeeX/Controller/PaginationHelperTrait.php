<?php

/*
 * Copyright 2023 Google LLC
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

namespace Apigee\Edge\Api\ApigeeX\Controller;

use Apigee\Edge\Api\ApigeeX\Structure\PagerInterface;
use Apigee\Edge\Controller\BaseEndpointAwareControllerTrait;
use Apigee\Edge\Controller\ClientAwareControllerTrait;

/**
 * Utility methods for those controllers that supports paginated listing.
 *
 * @see \Apigee\Edge\Api\ApigeeX\Controller\PaginatedEntityListingControllerInterface
 */
trait PaginationHelperTrait
{
    use BaseEndpointAwareControllerTrait;
    use ClientAwareControllerTrait;

    /**
     * {@inheritdoc}
     */
    public function createPager(int $limit = 0, ?string $pageToken = null): PagerInterface
    {
        // Create an anonymous class here because this class should not exist and be in use
        // in those controllers that do not work with entities that belongs to an organization.
        $pager = new class() implements PagerInterface {
            protected $pageToken;

            protected $limit;

            /**
             * {@inheritdoc}
             */
            public function getPageToken(): ?string
            {
                return $this->pageToken;
            }

            /**
             * {@inheritdoc}
             */
            public function getLimit(): int
            {
                return $this->limit;
            }

            /**
             * {@inheritdoc}
             */
            public function setPageToken(?string $pageToken): ?string
            {
                $this->pageToken = $pageToken;

                return $this->pageToken;
            }

            /**
             * {@inheritdoc}
             */
            public function setLimit(int $limit): int
            {
                $this->limit = $limit;

                return $this->limit;
            }
        };

        $pager->setLimit($limit);
        $pager->setPageToken($pageToken);

        return $pager;
    }

    /**
     * Loads paginated list of entities from Apigee X.
     *
     * @param \Apigee\Edge\Api\ApigeeX\Structure\PagerInterface|null $pager
     *   Pager.
     * @param array $query_params
     *   Additional query parameters.
     * @param string $key_provider
     *   Getter method on the entity that should provide a unique array key.
     *
     * @return \Apigee\Edge\Entity\EntityInterface[]
     *   Array of entity objects.
     *
     * @psalm-suppress PossiblyNullArrayOffset $tmp->id() is always not null here.
     */
    protected function listEntities(PagerInterface $pager = null, array $query_params = [], string $key_provider = 'id'): array
    {
        if ($pager) {
            $responseArray = $this->getResultsInRange($pager, $query_params);
            // Ignore entity type key from response, ex.: developer,
            // apiproduct, etc.
            $responseArray = reset($responseArray);

            return $this->responseArrayToArrayOfEntities($responseArray, $key_provider);
        } else {
            // Pass an empty pager to load all entities.
            $responseArray = $this->getResultsInRange($this->createPager(), $query_params);
            // Ignore entity type key from response, ex.: developer, apiproduct,
            // etc.
            $responseArray = reset($responseArray);
            // Appgroup can be empty.
            if (empty($responseArray) || !is_array($responseArray)) {
                return [];
            }
            $entities = $this->responseArrayToArrayOfEntities($responseArray, $key_provider);
            $lastEntity = end($entities);
            $lastId = $lastEntity->{$key_provider}();
            do {
                $tmp = $this->getResultsInRange($this->createPager(0, $lastId), $query_params);
                // Ignore entity type key from response, ex.: developer,
                // apiproduct, etc.
                $tmp = reset($tmp);
                // Remove the first item from the list because it is the same
                // as the last item of $entities at this moment.
                // Apigee X response always starts with the requested entity
                // (pageToken).
                array_shift($tmp);
                $tmpEntities = $this->responseArrayToArrayOfEntities($tmp, $key_provider);

                if (count($tmpEntities) > 0) {
                    // The returned entity array is keyed by entity id which
                    // is unique so we can do this.
                    $entities += $tmpEntities;
                    $lastEntity = end($tmpEntities);
                    $lastId = $lastEntity->{$key_provider}();
                } else {
                    $lastId = false;
                }
            } while ($lastId);

            return $entities;
        }
    }

    /**
     * Gets entities and entity ids in a provided range from Apigee X.
     *
     * @param \Apigee\Edge\Api\ApigeeX\Structure\PagerInterface $pager
     *   limit object with configured pageToken and limit.
     * @param array $query_params
     *   Query parameters for the API call.
     * @param bool $expandCompatability
     *   If the API response requires backwards compatibility with the way Edge
     *   formats it's responses.
     *
     * @see \Apigee\Edge\Utility\ResponseToArrayHelper::responseToArray()
     *
     * @return array
     *   API response parsed as an array.
     */
    private function getResultsInRange(PagerInterface $pager, array $query_params, bool $expandCompatibility = false): array
    {
        $query_params['pageToken'] = $pager->getPageToken();
        // Do not add 0 unnecessarily to the query parameters.
        if ($pager->getLimit() > 0) {
            $query_params['pageSize'] = $pager->getLimit();
        }
        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($query_params));
        $response = $this->getClient()->get($uri);

        return $this->responseToArray($response, $expandCompatibility);
    }
}
