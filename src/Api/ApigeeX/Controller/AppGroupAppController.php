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

use Apigee\Edge\Api\ApigeeX\Entity\AppGroupApp;
use Apigee\Edge\Api\ApigeeX\Serializer\AppGroupEntitySerializer;
use Apigee\Edge\Api\Management\Controller\AppByOwnerController;
use Apigee\Edge\Api\Management\Controller\OrganizationController;
use Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Apigee\Edge\Structure\PagerInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class AppGroupAppController.
 */
class AppGroupAppController extends AppByOwnerController implements AppGroupAppControllerInterface
{
    use AppGroupAwareControllerTrait;

    /**
     * @var \Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface
     */
    protected $organizationController;

    /**
     * AppGroupAppController constructor.
     *
     * @param string $organization
     * @param string $appGroupName
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     * @param \Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface|null $organizationController
     */
    public function __construct(
        string $organization,
        string $appGroupName,
        ClientInterface $client,
        ?EntitySerializerInterface $entitySerializer = null,
        ?OrganizationControllerInterface $organizationController = null
    ) {

        $this->appGroupName = $appGroupName;
        $entitySerializer = $entitySerializer ?? new AppGroupEntitySerializer();
        $this->organizationController = $organizationController ?? new OrganizationController($client);
        parent::__construct($organization, $client, $entitySerializer);
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/organizations/{$this->organization}/appgroups/{$this->appGroupName}/apps");
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityClass(): string
    {
        return AppGroupApp::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getOrganizationController(): OrganizationControllerInterface
    {
        return $this->organizationController;
    }

    /**
     * Override the getEntityIds() method, for Hybrid compatibility.
     *
     * Hybrid/AppGroup does not support the "expand=false" query parameter.
     *
     * {@inheritdoc}
     */
    public function getEntityIds(PagerInterface $pager = null): array
    {
        $uri = $this->getBaseEndpointUri();
        $response = $this->getClient()->get($uri);

        return $this->responseToArray($response, true);
    }

    /**
     * Override the getEntities() method, for Hybrid compatibility.
     *
     * AppGroup does not support the "expand=false" query parameter.
     *
     * {@inheritdoc}
     *
     * @return \Apigee\Edge\Entity\EntityInterface[]
     */
    public function getEntities(): array
    {
        $uri = $this->getBaseEndpointUri();
        $response = $this->getClient()->get($uri);
        $responseArray = $this->responseToArray($response);
        // Ignore entity type key from response, ex.: apiProduct.
        $responseArray = reset($responseArray);

        return $this->responseArrayToArrayOfEntities($responseArray);
    }
}
