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

use Apigee\Edge\Api\Specstore\Entity\Folder;
use Apigee\Edge\Api\Specstore\Entity\SpecstoreObject;
use Apigee\Edge\Api\Specstore\Serializer\CollectionSerializer;
use Apigee\Edge\Controller\EntityController;

/**
 *
 * Class FolderController allows for CRUD operations for folders
 *
 * In addition it allows to get the contents of a Folder
 *
 * @package Apigee\Edge\Api\Specstore\Controller
 */
class FolderController extends EntityController
{
    use SpecstoreEntityControllerTrait;

    public function getFolderContents(Folder $entity)
    {
        $response = $this->getClient()->get($entity->getContents());
        $collectionSerializer = new CollectionSerializer();

        return $collectionSerializer->deserialize(
            (string)$response->getBody(),
            Collection::class,
            'json'
        );
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
        return \Apigee\Edge\Api\Specstore\Entity\Folder::class;
    }

    protected function getCreateEndpointUri()
    {
        return 'folders';
    }

    /**
     * Map a folder path for the current specstore object
     *
     * @param SpecstoreObject $entity
     * @return string
     */
    public function getPath(SpecstoreObject $entity): string
    {
        $parent_folder_id = $entity->getFolder();
        $parent_specstore_folder = $this->load($parent_folder_id);
        if (!empty($parent_specstore_folder->getFolder())) {
            return $this->getPath($parent_specstore_folder) . "/" . $entity->getName();
        } else {
            return $entity->getName();
        }
    }
}
