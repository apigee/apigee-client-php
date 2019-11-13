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

use Apigee\Edge\Exception\RuntimeException;
use Apigee\Edge\Structure\PagerInterface;
use Apigee\Edge\Utility\OrganizationFeatures;
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
     */
    protected function listEntities(PagerInterface $pager = null, array $query_params = [], string $key_provider = 'id'): array
    {
        /** @var \Apigee\Edge\Api\Management\Entity\OrganizationInterface $organization */
        $organization = $this->getOrganizationController()->load($this->getOrganisationName());

        if (OrganizationFeatures::isPaginationAvailable($organization)) {
            return $this->listEntitiesWithCps($pager, $query_params, $key_provider);
        } else {
            $this->triggerCpsSimulationNotice($pager);

            return $this->listEntitiesWithoutCps($pager, $query_params, $key_provider);
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
        /** @var \Apigee\Edge\Api\Management\Entity\OrganizationInterface $organization */
        $organization = $this->getOrganizationController()->load($this->getOrganisationName());

        if (OrganizationFeatures::isCpsEnabled($organization)) {
            return $this->listEntityIdsWithCps($pager, $query_params);
        } else {
            $this->triggerCpsSimulationNotice($pager);

            return $this->listEntityIdsWithoutCps($pager, $query_params);
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
     * Real paginated entity listing on organization with CPS support.
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
    private function listEntitiesWithCps(PagerInterface $pager = null, array $query_params = [], string $key_provider = 'id'): array
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
     * Simulates paginated entity listing on organization without CPS support.
     *
     * For example, on on-prem installations.
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
     */
    private function listEntitiesWithoutCps(PagerInterface $pager = null, array $query_params = [], string $key_provider = 'id'): array
    {
        $query_params = [
                'expand' => 'true',
            ] + $query_params;

        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($query_params));
        $response = $this->getClient()->get($uri);
        $responseArray = $this->responseToArray($response);
        // Ignore entity type key from response, ex.: apiProduct.
        $responseArray = reset($responseArray);

        $entities = $this->responseArrayToArrayOfEntities($responseArray, $key_provider);

        return $pager ? $this->simulateCpsPagination($pager, $entities, array_keys($entities)) : $entities;
    }

    /**
     * Gets entities and entity ids in a provided range from Apigee Edge.
     *
     * This method for organizations with CPS enabled.
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

    /**
     * Triggers an E_USER_NOTICE if pagination is used in a non-CPS org.
     *
     * @param \Apigee\Edge\Structure\PagerInterface|null $pager
     *   Pager.
     */
    private function triggerCpsSimulationNotice(PagerInterface $pager = null): void
    {
        // Trigger an E_USER_NOTICE error if pagination feature needs to
        // be simulated on an organization without CPS to let developers
        // know that the Apigee PHP API client executed a workaround.
        // If suppressing all E_NOTICE level errors in an environment is
        // undesired then setting the following environment variable to
        // falsy value can also suppress this notice.
        if ($pager && !getenv('APIGEE_EDGE_PHP_CLIENT_SUPPRESS_CPS_SIMULATION_NOTICE')) {
            trigger_error('Apigee Edge PHP Client: Simulating CPS pagination on an organization that does not have CPS support. https://docs.apigee.com/api-platform/reference/cps', E_USER_NOTICE);
        }
    }

    /**
     * Real paginated entity id listing on organization with CPS support.
     *
     * @param \Apigee\Edge\Structure\PagerInterface|null $pager
     *   Pager.
     * @param array $query_params
     *   Additional query parameters.
     *
     * @return string[]
     *   Array of entity ids.
     */
    private function listEntityIdsWithCps(PagerInterface $pager = null, array $query_params = []): array
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
     * Simulates paginated entity id listing on organization without CPS.
     *
     * @param \Apigee\Edge\Structure\PagerInterface|null $pager
     *   Pager.
     * @param array $query_params
     *   Additional query parameters.
     *
     * @return string[]
     *   Array of entity ids.
     */
    private function listEntityIdsWithoutCps(PagerInterface $pager = null, array $query_params = []): array
    {
        $query_params = [
                'expand' => 'false',
            ] + $query_params;

        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($query_params));
        $response = $this->getClient()->get($uri);

        $ids = $this->responseToArray($response);

        // Re-key the array from 0 if CPS had to be simulated.
        return $pager ? array_values($this->simulateCpsPagination($pager, $ids)) : $ids;
    }

    /**
     * Simulates paginated response on an organization without CPS.
     *
     * @param \Apigee\Edge\Structure\PagerInterface $pager
     *   Pager.
     * @param array $result
     *   The non-paginated result returned by the API.
     * @param array|null $array_search_haystack
     *   Haystack for array_search, the needle is the start key from the pager.
     *   If it is null, then the haystack is the $result.
     *
     * @return array
     *   The paginated result.
     */
    private function simulateCpsPagination(PagerInterface $pager, array $result, array $array_search_haystack = null): array
    {
        $array_search_haystack = $array_search_haystack ?? $result;
        // If start key is null let's set it to the first key in the
        // result just like the API would do.
        $start_key = $pager->getStartKey() ?? reset($array_search_haystack);
        $offset = array_search($start_key, $array_search_haystack);
        // Start key has not been found in the response. Apigee Edge with
        // CPS enabled would return an HTTP 404, with error code
        // "keymanagement.service.[ENTITY_TYPE]_doesnot_exist" which would
        // trigger a ClientErrorException. We throw a RuntimeException
        // instead of that because it does not require to construct an
        // API response object.
        if (false === $offset) {
            throw new RuntimeException(sprintf('CPS simulation error: "%s" does not exist.', $start_key));
        }
        // The default pagination limit (aka. "count") on CPS supported
        // listing endpoints varies. When this script was written it was
        // 1000 on two endpoints and 100 on two app related endpoints,
        // namely List Developer Apps and List Company Apps. A
        // developer/company should not have 100 apps, this is
        // the reason why this limit is smaller. Therefore we choose to
        // use 1000 as pagination limit if it has not been set.
        // https://apidocs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/apiproducts-0
        // https://apidocs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/developers
        // https://apidocs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/apps
        return array_slice($result, $offset, $pager->getLimit() ?: 1000, true);
    }
}
