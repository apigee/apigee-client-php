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

use Apigee\Edge\Api\Management\Entity\Environment;
use Apigee\Edge\Api\Management\Serializer\EnvironmentSerializer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityController;
use Apigee\Edge\Controller\EntityCrudOperationsControllerTrait;
use Apigee\Edge\Controller\NonPaginatedEntityIdListingControllerTrait;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class EnvironmentController.
 */
class EnvironmentController extends EntityController implements EnvironmentControllerInterface
{
    use EntityCrudOperationsControllerTrait;
    use NonPaginatedEntityIdListingControllerTrait;

    /**
     * EnvironmentController constructor.
     *
     * @param string $organization
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(
        string $organization,
        ClientInterface $client,
        ?EntitySerializerInterface $entitySerializer = null
    ) {
        $entitySerializer = $entitySerializer ?? new EnvironmentSerializer();
        parent::__construct($organization, $client, $entitySerializer);
    }

    /**
     * Override the getEntityIds() method, for Hybrid compatibility.
     *
     * Environments are not entities, so Hybrid does not support the "expand=false" query parameter.
     *
     * @inheritdoc
     */
    public function getEntityIds(): array
    {
        $uri = $this->getBaseEndpointUri();
        $response = $this->getClient()->get($uri);

        return $this->responseToArray($response);
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/organizations/{$this->organization}/environments");
    }

    /**
     * @inheritdoc
     */
    protected function getEntityClass(): string
    {
        return Environment::class;
    }
}
