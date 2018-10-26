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

use Apigee\Edge\Api\Management\Entity\Developer;
use Apigee\Edge\Api\Management\Entity\DeveloperInterface;
use Apigee\Edge\Api\Management\Exception\DeveloperNotFoundException;
use Apigee\Edge\Api\Management\Serializer\DeveloperSerializer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityCrudOperationsControllerTrait;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Controller\PaginatedEntityController;
use Apigee\Edge\Controller\PaginatedEntityIdListingControllerTrait;
use Apigee\Edge\Controller\PaginatedEntityListingControllerTrait;
use Apigee\Edge\Controller\PaginationHelperTrait;
use Apigee\Edge\Controller\StatusAwareEntityControllerTrait;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Apigee\Edge\Structure\PagerInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class DeveloperController.
 */
class DeveloperController extends PaginatedEntityController implements DeveloperControllerInterface
{
    use AttributesAwareEntityControllerTrait;
    use EntityCrudOperationsControllerTrait;
    use EntityListingControllerTrait;
    use PaginatedEntityListingControllerTrait {
        getEntities as private traitGetEntities;
    }
    use PaginatedEntityIdListingControllerTrait;
    use PaginationHelperTrait;
    use StatusAwareEntityControllerTrait;

    /**
     * DeveloperController constructor.
     *
     * @param string $organization
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     * @param OrganizationControllerInterface|null $organizationController
     */
    public function __construct(
        string $organization,
        ClientInterface $client,
        ?EntitySerializerInterface $entitySerializer = null,
        ?OrganizationControllerInterface $organizationController = null
    ) {
        $entitySerializer = $entitySerializer ?? new DeveloperSerializer();
        parent::__construct($organization, $client, $entitySerializer, $organizationController);
    }

    /**
     * @inheritdoc
     */
    public function getDeveloperByApp(string $appName): DeveloperInterface
    {
        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query(['app' => $appName]));
        $responseArray = $this->responseToArray($this->client->get($uri));
        // When developer has not found by app we are still getting back HTTP 200 with an empty developer array.
        if (empty($responseArray['developer'])) {
            throw new DeveloperNotFoundException(
                $this->client->getJournal()->getLastResponse(),
                $this->client->getJournal()->getLastRequest()
            );
        }
        $values = reset($responseArray['developer']);

        return $this->entitySerializer->denormalize($values, $this->getEntityClass());
    }

    /**
     * @inheritdoc
     */
    public function getEntities(
        PagerInterface $pager = null,
        string $key_provider = 'id'
    ): array {
        // The getEntityIds() returns email addresses so we should use email
        // addresses as keys in the array as well.
        return $this->traitGetEntities($pager, 'getEmail');
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()
            ->createUri("/organizations/{$this->organization}/developers");
    }

    /**
     * @inheritdoc
     */
    protected function getEntityClass(): string
    {
        return Developer::class;
    }
}
