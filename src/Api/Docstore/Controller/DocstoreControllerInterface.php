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
     * @param Folder $entity
     */
    public function createFolder(Folder $entity): void;

    /**
     * @param Doc $entity
     */
    public function createSpec(Doc $entity): void;

    /**
     * @param Doc $entity
     * @param string $content
     */
    public function uploadJsonSpec(Doc $entity, string $content): void;

    /**
     * @param Doc $entity
     *
     * @return string
     */
    public function getSpecContentsAsJson(Doc $entity): string;

    /**
     * @param DocstoreObject $entity
     *
     * @return string
     */
    public function getPath(DocstoreObject $entity): string;

    /**
     * @param Folder $entity
     *
     * @return Collection
     */
    public function getFolderContents(Folder $entity): Collection;

    /**
     * @param string $path
     * @param DocstoreObject|null $parent
     *
     * @return mixed
     */
    public function loadByPath(string $path, DocstoreObject $parent = null);
}
