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

use Apigee\Edge\Api\Docstore\Entity\Collection;
use Apigee\Edge\Api\Docstore\Entity\Doc;
use Apigee\Edge\Api\Docstore\Entity\DocstoreObject;
use Apigee\Edge\Api\Docstore\Entity\Folder;
use Apigee\Edge\Api\Docstore\Serializer\DocstoreSerializer;
use Apigee\Edge\Api\Monetization\Entity\Entity;
use Apigee\Edge\Controller\EntityController;
use Apigee\Edge\Entity\EntityInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class DocstoreController allows for CRUD operations for Docstore entities (Folder and Doc).
 */
class DocstoreController extends EntityController implements DocstoreControllerInterface
{
    /**
     * @inheritdoc
     */
    public function create(EntityInterface $entity): void
    {
        if ($entity instanceof Doc) {
            $this->createSpec($entity);
        } elseif ($entity instanceof Folder) {
            $this->createFolder($entity);
        }
    }

    /**
     * @param Folder $entity
     *
     * @throws \Http\Client\Exception
     */
    public function createFolder(Folder $entity): void
    {
        if (null == $entity->getFolder()) {
            $entity->setFolder($this->getHomeFolderId());
        }
        $response = $this->getClient()->post('folders',
            $this->getEntitySerializer()->serialize($entity, 'json'),
            $this->getHeaders());
        $this->getEntitySerializer()->setPropertiesFromResponse($response, $entity);
        $entity->setEtag($response->getHeader('ETag')[0]);
    }

    /**
     * @param Doc $entity
     *
     * @throws \Http\Client\Exception
     */
    public function createSpec(Doc $entity): void
    {
        if (null == $entity->getFolder()) {
            $entity->setFolder($this->getHomeFolderId());
        }
        $response = $this->getClient()->post('specs/new',
            $this->getEntitySerializer()->serialize($entity, 'json'),
            $this->getHeaders());
        $this->getEntitySerializer()->setPropertiesFromResponse($response, $entity);
        $entity->setEtag($response->getHeader('ETag')[0]);
    }

    /**
     * @inheritdoc
     */
    public function load(string $entityId): EntityInterface
    {
        $response = $this->getClient()->get($this->getEntityEndpointUri($entityId), $this->getHeaders());

        return $this->parseDocstoreResponse($response);
    }

    /**
     * @param DocstoreObject $entity
     *
     * @throws \Http\Client\Exception
     */
    public function update(EntityInterface $entity): void
    {
        $updateReq = [];
        $updateReq['folder'] = $entity->getFolder();
        $updateReq['name'] = $entity->getName();
        if (null !== $entity->getDescription()) {
            $updateReq['description'] = $entity->getDescription();
        }
        $updateReq['isTrashed'] = $entity->getIsTrashed();
        // Update an existing entity.
        $response = $this->getClient()->patch(
            $this->getEntityEndpointUri($entity->id()),
            $this->getEntitySerializer()->serialize($updateReq, 'json'),
            $this->getHeaders() + ['If-Match' => $entity->getEtag()]
        );
        $this->getEntitySerializer()->setPropertiesFromResponse($response, $entity);
    }

    /**
     * @param string $entityId
     *
     * @throws \Http\Client\Exception
     */
    public function delete(string $entityId): void
    {
        $this->getClient()->delete($this->getEntityEndpointUri($entityId), null, $this->getHeaders());
    }

    /**
     * Upload a OpenAPI file to the backend.
     *
     * This is what the API supports today.
     * Uploading a JSON file and setting the content-type to application/x-yaml
     * This will change in the future
     *
     * @param Doc $entity
     * @param $content
     *
     * @throws \Http\Client\Exception
     */
    public function uploadJsonSpec(Doc $entity, string $content): void
    {
        $this->getClient()->put(
            $entity->getContent(),
            $content,
            $this->getHeaders() + ['Content-Type' => 'application/x-yaml']);
    }

    /**
     * Get the contents of the spec.
     *
     * Always returns application/json
     *
     * @param Doc $entity
     *
     * @throws \Http\Client\Exception
     *
     * @return string
     */
    public function getSpecContentsAsJson(Doc $entity): string
    {
        $response = $this->getClient()->get($entity->getContent(),
            $this->getHeaders() + ['Accept' => 'application/json']);

        return (string) $response->getBody();
    }

    /**
     * Map a folder path for the current Docstore object.
     *
     * @param DocstoreObject $entity
     *
     * @return string
     */
    public function getPath(DocstoreObject $entity): string
    {
        $parentFolderId = $entity->getFolder();
        $parentDocstoreFolder = $this->load($parentFolderId);
        if (!empty($parentDocstoreFolder->getFolder())) {
            return $this->getPath($parentDocstoreFolder) . '/' . ($entity->getName() ?: '');
        } else {
            return $entity->getName() ?: '';
        }
    }

    /**
     * Given the path load the Docstore object.
     *
     * @returns null|DocstoreObject
     */
    public function loadByPath(string $path, DocstoreObject $parent = null)
    {
        if (null === $parent) {
            $parent = $this->load('/homeFolder');
        }
        $contents = $this->getFolderContents($parent)->getContents();
        $pathArr = explode('/', $path);
        $currentFolder = array_shift($pathArr);
        $foundObj = null;
        foreach ($contents as $docstoreObj) {
            if ($docstoreObj->getName() == $currentFolder) {
                $foundObj = $docstoreObj;
                break;
            }
        }
        if (empty($pathArr)) {
            return $foundObj;
        } else {
            return null === $foundObj ? null : $this->loadByPath(implode('/', $pathArr), $foundObj);
        }
    }

    /**
     * Return the contents of the Folder.
     *
     * @param Folder $entity
     *
     * @throws \Http\Client\Exception
     *
     * @return Collection
     */
    public function getFolderContents(Folder $entity): Collection
    {
        $response = $this->getClient()->get($entity->getContents(), $this->getHeaders());
        $collectionSerializer = new DocstoreSerializer();

        return $collectionSerializer->deserialize(
            (string) $response->getBody(),
            Collection::class,
            'json'
        );
    }

    /**
     * Docstore entity id start with a "/".
     *
     * @inheritdoc
     */
    protected function getEntityEndpointUri(string $entityId): UriInterface
    {
        return $this->getBaseEndpointUri()->withPath("{$this->getBaseEndpointUri()}{$entityId}");
    }

    /**
     * Returns the API endpoint that the controller communicates with.
     *
     * In case of an entity that belongs to an organisation it should return organization/[orgName]/[endpoint].
     *
     * @return \Psr\Http\Message\UriInterface
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri('');
    }

    /**
     * @return array
     */
    protected function getHeaders()
    {
        return ['X-Org-Name' => $this->getOrganisationName()];
    }

    /**
     * Returns the fully-qualified class name of the entity that this controller works with.
     *
     * @return string
     */
    protected function getEntityClass(): string
    {
        return DocstoreObject::class;
    }

    /**
     * Return the identifier for the home folder of the org.
     *
     * @return null|string
     */
    private function getHomeFolderId()
    {
        static $homeFolderId;
        if (!$homeFolderId) {
            $homeFolder = $this->load('/homeFolder');
            $homeFolderId = $homeFolder->id();
        }

        return $homeFolderId;
    }

    /**
     * Parses the DocStore response and generates either a Folder/Doc object.
     */
    private function parseDocstoreResponse(ResponseInterface $response): DocstoreObject
    {
        $responseBody = (string) $response->getBody();
        $docstoreObj = json_decode($responseBody, true);
        $object = $this->getEntitySerializer()->deserialize(
            $responseBody,
            ('Folder' === $docstoreObj['kind'] ? Folder::class : Doc::class),
            'json'
        );
        if (!empty($response->getHeader('ETag'))) {
            $object->setEtag($response->getHeader('ETag')[0]);
        }

        return $object;
    }
}
