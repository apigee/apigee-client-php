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

namespace Apigee\Edge\Api\Docstore\Entity;

/**
 * Collection object in Docstore.
 */
class Collection extends DocstoreEntity
{
    /**
     * @var string
     */
    protected $kind = 'Collection';
    /**
     * @var array
     */
    protected $contents = [];

    /**
     * Returns the array of DocstoreEntity objects.
     *
     * @return DocstoreEntityInterface[]
     */
    public function getContents(): array
    {
        return $this->contents;
    }

    /**
     * Set the array of DocStoreEntity Objects on this collection.
     *
     * @param $contents DocstoreEntityInterface[]
     */
    public function setContents($contents): void
    {
        $this->contents = $contents;
    }
}
