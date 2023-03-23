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

use Apigee\Edge\Api\ApigeeX\Entity\AppGroup;
use Apigee\Edge\Api\ApigeeX\Serializer\AppGroupSerializer;
use Apigee\Edge\Api\Management\Controller\AttributesAwareEntityControllerTrait;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityCreateOperationControllerTrait;
use Apigee\Edge\Controller\EntityController;
use Apigee\Edge\Controller\EntityCrudOperationsControllerTrait;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Controller\StatusAwareEntityControllerTrait;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class AppGroupController.
 */
class AppGroupController extends EntityController implements AppGroupControllerInterface
{
    use AttributesAwareEntityControllerTrait;
    use EntityCrudOperationsControllerTrait;
    use EntityListingControllerTrait;
    use StatusAwareEntityControllerTrait;
    use EntityCreateOperationControllerTrait;

    /**
     * AppGroupController constructor.
     *
     * @param string $organization
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     * @param \Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface|null $organizationController
     */
    public function __construct(string $organization, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        $entitySerializer = $entitySerializer ?? new AppGroupSerializer();
        parent::__construct($organization, $client, $entitySerializer);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntities(): array
    {
        $uri = $this->getBaseEndpointUri();
        $response = $this->client->get($uri);
        $responseArray = $this->responseToArray($response);
        // Ignore entity type key from response.
        $responseArray = reset($responseArray);

        // Appgroup can be empty.
        if (empty($responseArray)) {
            return [];
        }

        return $this->responseArrayToArrayOfEntities($responseArray);
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/organizations/{$this->organization}/appgroups");
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityClass(): string
    {
        return AppGroup::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildEntityCreatePayload(EntityInterface $entity, array $context = []): string
    {
        $entitySerializer = $this->getEntitySerializer()->serialize($entity, 'json', $context);

        // decode json to associative array
        $payload = json_decode($entitySerializer, true);
        // Removing property which is not supported for AppGroup.
        unset($payload['apps']);

        return $this->getEntitySerializer()->serialize($payload, 'json', $context);
    }
}
