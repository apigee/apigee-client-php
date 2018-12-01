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
use Apigee\Edge\Api\Docstore\Entity\DocstoreEntity;
use Apigee\Edge\Api\Docstore\Entity\DocstoreEntityInterface;
use Apigee\Edge\Api\Docstore\Entity\Folder;
use Apigee\Edge\Api\Docstore\Serializer\DocstoreSerializer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityController;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class DocstoreController allows for CRUD operations for Docstore entities (Folder and Doc).
 */
class DocstoreController extends EntityController implements DocstoreControllerInterface
{
    /**
     * DocstoreController constructor.
     *
     * @param string $organization
     * @param ClientInterface $client
     * @param EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $organization, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        $entitySerializer = $entitySerializer ?? new DocstoreSerializer();
        parent::__construct($organization, $client, $entitySerializer);
    }

    /**
     * @inheritdoc
     */
    public function create(EntityInterface $entity): void
    {
        if ($entity instanceof Doc) {
            $this->createDoc($entity);
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
        $this->parseDocstoreResponse($response, $entity);
    }

    /**
     * @param Doc $entity
     *
     * @throws \Http\Client\Exception
     */
    public function createDoc(Doc $entity): void
    {
        if (null == $entity->getFolder()) {
            $entity->setFolder($this->getHomeFolderId());
        }
        $response = $this->getClient()->post('specs/new',
            $this->getEntitySerializer()->serialize($entity, 'json'),
            $this->getHeaders());
        $this->parseDocstoreResponse($response, $entity);
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
     * @param $entity EntityInterface
     *
     * @throws \Http\Client\Exception
     */
    public function update(EntityInterface $entity): void
    {
        $updateReq = [];
        $entityId = $entity->id();
        if ($entity instanceof DocstoreEntityInterface && !empty($entityId)) {
            $updateReq['folder'] = $entity->getFolder() ?? $this->getHomeFolderId();
            $updateReq['name'] = $entity->getName();
            if (null !== $entity->getDescription()) {
                $updateReq['description'] = $entity->getDescription();
            }
            $updateReq['isTrashed'] = $entity->getIsTrashed();
            // Update an existing entity.
            $response = $this->getClient()->patch(
                $this->getEntityEndpointUri($entityId),
                $this->getEntitySerializer()->serialize($updateReq, 'json'),
                $this->getHeaders() + ['If-Match' => $entity->getEtag()]
            );
            $this->parseDocstoreResponse($response, $entity);
        }
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
        $contentUrl = $entity->getContent();
        if (!empty($contentUrl)) {
            $this->getClient()->put(
                $contentUrl,
                $content,
                $this->getHeaders() + ['Content-Type' => 'application/x-yaml']);
        }
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
     * @return string|null
     */
    public function getSpecContentsAsJson(Doc $entity): ?string
    {
        $contentUrl = $entity->getContent();
        if (!empty($contentUrl)) {
            $response = $this->getClient()->get($contentUrl,
                $this->getHeaders() + ['Accept' => 'application/json']);

            return (string) $response->getBody();
        }

        return null;
    }

    /**
     * Map a folder path for the current Docstore object.
     *
     * @param DocstoreEntityInterface $entity
     *
     * @return string
     */
    public function getPath(DocstoreEntityInterface $entity): string
    {
        $parentFolderId = $entity->getFolder();
        if (!empty($parentFolderId)) {
            /* @var $parentDocstoreFolder Folder */
            $parentDocstoreFolder = $this->load($parentFolderId);
            if (($parentDocstoreFolder instanceof Folder) && !empty($parentDocstoreFolder->getFolder())) {
                return $this->getPath($parentDocstoreFolder) . '/' . ($entity->getName() ?: '');
            }
        }

        return $entity->getName() ?: '';
    }

    /**
     * Given the path load the Docstore object.
     *
     * @returns null|DocstoreEntityInterface
     */
    public function loadByPath(string $path, DocstoreEntityInterface $parent = null): ?DocstoreEntityInterface
    {
        if (null === $parent) {
            $parent = $this->load('/homeFolder');
        }
        $contents = $this->getFolderContents($parent);
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
     * @return array
     */
    public function getFolderContents(Folder $entity): array
    {
        $folderContentUrl = $entity->getContents();
        if (!empty($folderContentUrl)) {
            $response = $this->getClient()->get($folderContentUrl, $this->getHeaders());
            $collectionSerializer = new DocstoreSerializer();

            /* @var $collection Collection */
            $collection = $collectionSerializer->deserialize(
                (string) $response->getBody(),
                Collection::class,
                'json'
            );

            return $collection->getContents();
        } else {
            return [];
        }
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
        return DocstoreEntity::class;
    }

    /**
     * Return the identifier for the home folder of the org.
     *
     * @return string
     */
    private function getHomeFolderId(): string
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
    private function parseDocstoreResponse(ResponseInterface $response, DocstoreEntityInterface $entity = null): DocstoreEntityInterface
    {
        if (null == $entity) {
            $responseBody = (string) $response->getBody();
            $docstoreObj = json_decode($responseBody, true);
            $entity = 'Folder' === $docstoreObj['kind'] ? new Folder() : new Doc();
        }
        $this->getEntitySerializer()->setPropertiesFromResponse($response, $entity);

        return $entity;
    }
}
