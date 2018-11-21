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
use Apigee\Edge\Controller\EntityController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class DocstoreController allows for CRUD operations for Docstore entities (Folder and Doc).
 */
class DocstoreController extends EntityController
{
    /**
     * @inheritdoc
     */
    public function create(DocstoreObject $entity): void
    {
        if (null == $entity->getFolder()) {
            $entity->setFolder($this->getHomeFolderId());
        }
        $response = $this->getClient()->post(
            ($entity instanceof Doc ? 'specs/new' : 'folders'),
            $this->getEntitySerializer()->serialize($entity, 'json'),
            $this->getHeaders());
        $this->getEntitySerializer()->setPropertiesFromResponse($response, $entity);
        $entity->setEtag($response->getHeader('ETag')[0]);
    }

    /**
     * @inheritdoc
     */
    public function load(string $entityId): DocstoreObject
    {
        $response = $this->getClient()->get($this->getEntityEndpointUri($entityId), $this->getHeaders());

        return $this->parseDocstoreResponse($response);
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
        if (null !== $entity->getDescription()) {
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

        return $this->parseDocstoreResponse($response);
    }

    /**
     * Upload a openAPI file to the backend.
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
    public function uploadJsonSpec(Doc $entity, $content): void
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
    public function getSpecContents(Doc $entity): string
    {
        $response = $this->getClient()->get($entity->getContent(),
            $this->getHeaders() + ['Accept' => 'application/json, text/plain, */*']);

        return (string) $response->getBody();
    }

    /**
     * Map a folder path for the current specstore object.
     *
     * @param DocstoreObject $entity
     *
     * @return string
     */
    public function getPath(DocstoreObject $entity): string
    {
        $parent_folder_id = $entity->getFolder();
        $parent_specstore_folder = $this->load($parent_folder_id);
        if (!empty($parent_specstore_folder->getFolder())) {
            return $this->getPath($parent_specstore_folder) . '/' . ($entity->getName() ?: '');
        } else {
            return $entity->getName() ?: '';
        }
    }

    /**
     * Given the path load the specstore object.
     *
     * @returns null|DocstoreObject
     */
    public function loadByPath(string $path, DocstoreObject $parent = null)
    {
        if (null === $parent) {
            $parent = $this->load('/homeFolder');
        }
        $contents = $this->getFolderContents($parent)->getContents();
        $path_arr = explode('/', $path);
        $current_folder = array_shift($path_arr);
        $found_obj = null;
        foreach ($contents as $specstoreObject) {
            if ($specstoreObject->getName() == $current_folder) {
                $found_obj = $specstoreObject;
                break;
            }
        }
        if (empty($path_arr)) {
            return $found_obj;
        } else {
            return null === $found_obj ? null : $this->loadByPath(implode('/', $path_arr), $found_obj);
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
        $response_body = (string) $response->getBody();
        $docstore_obj = json_decode($response_body, true);
        $object = $this->getEntitySerializer()->deserialize(
            $response_body,
            ('Folder' === $docstore_obj['kind'] ? Folder::class : Doc::class),
            'json'
        );
        if (!empty($response->getHeader('ETag'))) {
            $object->setEtag($response->getHeader('ETag')[0]);
        }

        return $object;
    }
}
