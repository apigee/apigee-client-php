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

use Apigee\Edge\Api\Docstore\Entity\Doc;
use Apigee\Edge\Api\Docstore\Entity\DocstoreEntity;
use Apigee\Edge\Api\Docstore\Entity\DocstoreEntityInterface;
use Apigee\Edge\Api\Docstore\Entity\Folder;
use Apigee\Edge\Api\Monetization\Controller\EntityCrudOperationsControllerInterface;
use Apigee\Edge\Controller\EntityControllerInterface;

/**
 * Interface DocstoreControllerInterface.
 */
interface DocstoreControllerInterface extends
    EntityControllerInterface ,
    EntityCrudOperationsControllerInterface
{
    /**
     * Create a folder in the Apigee Docstore.
     *
     * @param Folder $entity
     */
    public function createFolder(Folder $entity): void;

    /**
     * Create a Doc entity in the Apigee DocStore.
     *
     * @param Doc $entity
     */
    public function createDoc(Doc $entity): void;

    /**
     * Attach an OpenAPI spec in JSON format to the Docstore entity.
     *
     * @param Doc $entity
     * @param string $content
     */
    public function uploadJsonSpec(Doc $entity, string $content): void;

    /**
     * Return the OpenAPI spec in JSON format.
     *
     * @param Doc $entity
     *
     * @return string
     */
    public function getSpecContentsAsJson(Doc $entity): ?string;

    /**
     * Generate a path for the given Docstore entity.
     *
     * @param DocstoreEntityInterface $entity
     *
     * @return string
     */
    public function getPath(DocstoreEntityInterface $entity): string;

    /**
     * Load the Docstore entity from the given path relative to the entity being passed.
     *
     * @param string $path
     * @param DocstoreEntity|null $parent - if null is passed we traverse from the home folder of the given org
     *
     * @return DocstoreEntity
     */
    public function loadByPath(string $path, DocstoreEntityInterface $parent = null): ?DocstoreEntityInterface;

    /**
     * Get the contents of a given folder.
     *
     * @param Folder $entity
     *
     * @return DocstoreEntityInterface[]
     */
    public function getFolderContents(Folder $entity): array;
}
