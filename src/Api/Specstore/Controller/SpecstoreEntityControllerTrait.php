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

namespace Apigee\Edge\Api\Specstore\Controller;

use Apigee\Edge\Api\Specstore\Entity\SpecstoreObject;
use Psr\Http\Message\UriInterface;

/**
 * Trait SpecstoreEntityControllerTrait rewrites the CRUD operations
 * because the specstore APIs do not accept org name in the URL but need a new header
 * parameter (X-Org-Name).
 */
trait SpecstoreEntityControllerTrait
{
    /**
     * @inheritdoc
     */
    public function load(string $entityId): SpecstoreObject
    {
        $response = $this->getClient()->get($this->getEntityEndpointUri($entityId), $this->getHeaders());

        return $this->getEntitySerializer()->deserialize(
            (string)$response->getBody(),
            $this->getEntityClass(),
            'json'
        );
    }

    /**
     * @inheritdoc
     */
    public function create(SpecstoreObject $entity): void
    {
        $response = $this->getClient()->post(
            $this->getCreateEndpointUri(),
            $this->getEntitySerializer()->serialize($entity, 'json'),
            $this->getHeaders());
        $this->getEntitySerializer()->setPropertiesFromResponse($response, $entity);
    }

    /**
     * @inheritdoc
     *
     * @psalm-suppress PossiblyNullArgument $entity->id() is always not null here.
     */
    public function update(SpecstoreObject $entity): void
    {
        $uri = $this->getEntityEndpointUri($entity->id());
        // Update an existing entity.
        $response = $this->getClient()->patch(
            $uri,
            $this->getEntitySerializer()->serialize($entity, 'json'),
            $this->getHeaders()
        );
        $this->getEntitySerializer()->setPropertiesFromResponse($response, $entity);
    }

    /**
     * @inheritdoc
     */
    public function delete(string $entityId): SpecstoreObject
    {
        $response = $this->getClient()->delete($this->getEntityEndpointUri($entityId), $this->getHeaders());

        return $this->getEntitySerializer()->deserialize(
            (string)$response->getBody(),
            $this->getEntityClass(),
            'json'
        );
    }

    protected function getHeaders()
    {
        return ['X-Org-Name' => $this->getOrganisationName()];
    }

    /**
     * EntityID is always prefixed by a "/" in specstore apis.
     *
     * @inheritdoc
     */
    protected function getEntityEndpointUri(string $entityId): UriInterface
    {
        return $this->getBaseEndpointUri()->withPath("{$this->getBaseEndpointUri()}{$entityId}");
    }

    abstract protected function getCreateEndpointUri();
}
