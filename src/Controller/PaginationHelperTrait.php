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

use Apigee\Edge\Exception\CpsNotEnabledException;
use Apigee\Edge\Structure\PagerInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Utility methods for those controllers that supports paginated listing.
 *
 * @see \Apigee\Edge\Controller\PaginatedEntityListingControllerInterface
 */
trait PaginationHelperTrait
{
    use ClientAwareControllerTrait;
    use BaseEndpointAwareControllerTrait;
    use OrganizationControllerAwareTrait;
    use OrganizationAwareControllerTrait;

    /**
     * @inheritdoc
     */
    public function createPager(int $limit = 0, ?string $startKey = null): PagerInterface
    {
        /** @var \Apigee\Edge\Api\Management\Entity\OrganizationInterface $organization */
        $organization = $this->getOrganizationController()->load($this->getOrganisationName());
        if (!$organization->getPropertyValue('features.isCpsEnabled')) {
            throw new CpsNotEnabledException($this->getOrganisationName());
        }

        // Create an anonymous class here because this class should not exist and be in use
        // in those controllers that do not work with entities that belongs to an organization.
        $pager = new class() implements PagerInterface {
            protected $startKey;

            protected $limit;

            /**
             * @inheritdoc
             */
            public function getStartKey(): ?string
            {
                return $this->startKey;
            }

            /**
             * @inheritdoc
             */
            public function getLimit(): int
            {
                return $this->limit;
            }

            /**
             * @inheritdoc
             */
            public function setStartKey(?string $startKey): ?string
            {
                $this->startKey = $startKey;

                return $this->startKey;
            }

            /**
             * @inheritdoc
             */
            public function setLimit(int $limit): int
            {
                $this->limit = $limit;

                return $this->limit;
            }
        };

        $pager->setLimit($limit);
        $pager->setStartKey($startKey);

        return $pager;
    }

    /**
     * Loads paginated list of entities from Apigee Edge.
     *
     * @param \Apigee\Edge\Structure\PagerInterface|null $pager
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
        $query_params = [
            'expand' => 'true',
        ] + $query_params;

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
            if (empty($responseArray)) {
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
                // Apigee Edge response always starts with the requested entity
                // (startKey).
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
     * Loads entity ids from Apigee Edge.
     *
     * @param \Apigee\Edge\Structure\PagerInterface|null $pager
     *   Pager.
     * @param array $query_params
     *   Additional query parameters.
     *
     * @return string[]
     *   Array of entity ids.
     */
    protected function listEntityIds(PagerInterface $pager = null, array $query_params = []): array
    {
        $query_params = [
            'expand' => 'false',
        ] + $query_params;
        if ($pager) {
            return $this->getResultsInRange($pager, $query_params);
        } else {
            $ids = $this->getResultsInRange($this->createPager(), $query_params);
            if (empty($ids)) {
                return [];
            }
            $lastId = end($ids);
            do {
                $tmp = $this->getResultsInRange($this->createPager(0, $lastId), $query_params);
                // Remove the first item from the list because it is the same
                // as the current last item of $ids.
                // Apigee Edge response always starts with the requested entity
                // id (startKey).
                array_shift($tmp);

                if (count($tmp) > 0) {
                    $ids = array_merge($ids, $tmp);
                    $lastId = end($tmp);
                } else {
                    $lastId = false;
                }
            } while ($lastId);

            return $ids;
        }
    }

    /**
     * @inheritdoc
     */
    abstract protected function responseToArray(ResponseInterface $response): array;

    /**
     * @inheritdoc
     */
    abstract protected function responseArrayToArrayOfEntities(array $responseArray, string $keyGetter = 'id'): array;

    /**
     * Gets entities and entity ids in a provided range from Apigee Edge.
     *
     * @param \Apigee\Edge\Structure\PagerInterface $pager
     *   CPS limit object with configured startKey and limit.
     * @param array $query_params
     *   Query parameters for the API call.
     *
     * @return array
     *   API response parsed as an array.
     */
    private function getResultsInRange(PagerInterface $pager, array $query_params): array
    {
        $query_params['startKey'] = $pager->getStartKey();
        // Do not add 0 unnecessarily to the query parameters.
        if ($pager->getLimit() > 0) {
            $query_params['count'] = $pager->getLimit();
        }
        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($query_params));
        $response = $this->getClient()->get($uri);

        return $this->responseToArray($response);
    }
}
