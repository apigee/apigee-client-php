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

namespace Apigee\Edge\Api\Docstore\Controller;

use Apigee\Edge\Api\Docstore\Entity\DocstoreObject;
use Psr\Http\Message\UriInterface;

/**
 * Trait DocstoreEntityControllerTrait rewrites the CRUD operations
 * because the specstore APIs do not accept org name in the URL but need a new header
 * parameter (X-Org-Name).
 */
trait DocstoreEntityControllerTrait
{
    /**
     * @inheritdoc
     */
    public function load(string $entityId): DocstoreObject
    {
        $response = $this->getClient()->get($this->getEntityEndpointUri($entityId), $this->getHeaders());

        $object = $this->getEntitySerializer()->deserialize(
            (string)$response->getBody(),
            $this->getEntityClass(),
            'json'
        );
        $object->setEtag($response->getHeader('ETag')[0]);
        return $object;
    }

    /**
     * @inheritdoc
     */
    public function create(DocstoreObject $entity): void
    {
        if (null == $entity->getFolder()) {
            $entity->setFolder($this->getHomeFolderId());
        }
        $response = $this->getClient()->post(
            $this->getCreateEndpointUri(),
            $this->getEntitySerializer()->serialize($entity, 'json'),
            $this->getHeaders());
        $this->getEntitySerializer()->setPropertiesFromResponse($response, $entity);
        $entity->setEtag($response->getHeader('ETag')[0]);

    }

    /**
     * @inheritdoc
     *
     * @psalm-suppress PossiblyNullArgument $entity->id() is always not null here.
     */
    public function update(DocstoreObject $entity): void
    {
        $uri = $this->getEntityEndpointUri($entity->id());
        $update_arr = [];
        $update_arr['folder'] = $entity->getFolder();
        $update_arr['name'] = $entity->getName();
        if ($entity->getDescription() !== null) {
            $update_arr['description'] = $entity->getDescription();
        }
        $update_arr['isTrashed'] = $entity->getIsTrashed();
        // Update an existing entity.
        $response = $this->getClient()->patch(
            $uri,
            $this->getEntitySerializer()->serialize($update_arr, 'json'),
            $this->getHeaders() + ['If-Match' => $entity->getEtag()]
        );
        $this->getEntitySerializer()->setPropertiesFromResponse($response, $entity);
    }

    /**
     * @inheritdoc
     */
    public function delete(string $entityId): DocstoreObject
    {
        $response = $this->getClient()->delete($this->getEntityEndpointUri($entityId), null, $this->getHeaders());

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

    private function getHomeFolderId()
    {
        static $homeFolderId;
        if (!$homeFolderId) {

            $homeFolder = $this->load("/homeFolder");
            $homeFolderId = $homeFolder->id();
        }
        return $homeFolderId;
    }
}
