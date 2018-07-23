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

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Api\Management\Entity\DeveloperApp;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityCrudOperationsControllerTrait;
use Apigee\Edge\Controller\PaginatedEntityListingControllerTrait;
use Apigee\Edge\Controller\StatusAwareEntityControllerTrait;
use Apigee\Edge\Structure\PagerInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class DeveloperAppController.
 */
class DeveloperAppController extends AppByOwnerController implements DeveloperAppControllerInterface
{
    use AttributesAwareEntityControllerTrait;
    use AppControllerTrait;
    use EntityCrudOperationsControllerTrait;
    use PaginatedEntityListingControllerTrait {
        getEntities as private traitGetEntities;
    }
    use StatusAwareEntityControllerTrait;

    /** @var string Developer email or id. */
    protected $developerId;

    /**
     * DeveloperAppController constructor.
     *
     * @param string $organization
     * @param string $developerId
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Symfony\Component\Serializer\Normalizer\NormalizerInterface[]|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface[] $entityNormalizers
     * @param \Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface|null $organizationController
     */
    public function __construct(
        string $organization,
        string $developerId,
        ClientInterface $client,
        array $entityNormalizers = [],
        ?OrganizationControllerInterface $organizationController = null
    ) {
        $this->developerId = $developerId;
        $entityNormalizers = array_merge($entityNormalizers, $this->appEntityNormalizers());
        parent::__construct($organization, $client, $entityNormalizers, $organizationController);
    }

    /**
     * @inheritdoc
     *
     * We had to override this because pagination does not work the same
     * way on developer apps as on other endpoints with "expand=true" at this
     * moment.
     */
    public function getEntities(
        PagerInterface $pager = null,
        string $key_provider = 'id'
    ): array {
        $query_params = [
            'shallowExpand' => 'true',
        ];

        if ($pager) {
            $responseArray = $this->getResultsInRange($pager, $query_params);

            return $this->responseArrayToArrayOfEntities($responseArray, $key_provider);
        } else {
            $responseArray = $this->getResultsInRange($this->createPager(), $query_params);
            // Ignore entity type key from response, ex.: developer, apiproduct,
            // etc.
            $responseArray = reset($responseArray);
            $entities = $this->responseArrayToArrayOfEntities($responseArray, $key_provider);
            $lastEntity = end($entities);
            $lastId = $lastEntity->{$key_provider}();
            do {
                $tmp = $this->getResultsInRange($this->createPager(0, $lastId), $query_params);
                // Ignore entity type key from response, ex.: developer,
                // apiproduct, etc.
                $tmp = reset($tmp);
                // The "shallowExpand" does not repeat the entity with id in
                // "startKey" as the first element of the array so we
                // do not need to remove that.
                $tmpEntities = $this->responseArrayToArrayOfEntities($tmp, $key_provider);
                // The returned entity array is keyed by entity id which
                // is unique so we can do this.
                $entities += $tmpEntities;

                if (count($tmpEntities) > 1) {
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
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/organizations/{$this->organization}/developers/{$this->developerId}/apps");
    }

    /**
     * @inheritdoc
     */
    protected function getEntityClass(): string
    {
        return DeveloperApp::class;
    }
}
