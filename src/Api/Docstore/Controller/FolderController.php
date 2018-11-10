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
use Apigee\Edge\Api\Docstore\Entity\DocstoreObject;
use Apigee\Edge\Api\Docstore\Entity\Folder;
use Apigee\Edge\Api\Docstore\Serializer\CollectionSerializer;
use Apigee\Edge\Controller\EntityController;

/**
 * Class FolderController allows for CRUD operations for folders.
 *
 * In addition it allows to get the contents of a Folder
 */
class FolderController extends EntityController
{
    use DocstoreEntityControllerTrait;

    /**
     * @param Folder $entity
     *
     * @throws \Http\Client\Exception
     *
     * @return Collection
     */
    public function getFolderContents(Folder $entity): Collection
    {
        $response = $this->getClient()->get($entity->getContents(), $this->getHeaders());
        $collectionSerializer = new CollectionSerializer();

        return $collectionSerializer->deserialize(
            (string)$response->getBody(),
            Collection::class,
            'json'
        );
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
     * Returns the API endpoint that the controller communicates with.
     *
     * In case of an entity that belongs to an organisation it should return organization/[orgName]/[endpoint].
     *
     * @return \Psr\Http\Message\UriInterface
     */
    protected function getBaseEndpointUri(): \Psr\Http\Message\UriInterface
    {
        return $this->client->getUriFactory()->createUri('');
    }

    /**
     * Returns the fully-qualified class name of the entity that this controller works with.
     *
     * @return string
     */
    protected function getEntityClass(): string
    {
        return \Apigee\Edge\Api\Docstore\Entity\Folder::class;
    }

    protected function getCreateEndpointUri()
    {
        return 'folders';
    }
}
