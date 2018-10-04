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

namespace Apigee\Edge\Api\Specstore\Entity;

use Apigee\Edge\Entity\Entity;
use Apigee\Edge\Entity\Property\DescriptionPropertyAwareTrait;
use Apigee\Edge\Entity\Property\NamePropertyAwareTrait;

abstract class SpecstoreObject extends Entity
{
    use NamePropertyAwareTrait;
    use DescriptionPropertyAwareTrait;
    use \Apigee\Edge\Entity\CommonEntityPropertiesAwareTrait;

    protected $permissions;
    protected $isTrashed;
    protected $self;
    protected $folder;

    protected $kind;

    public function getKind()
    {
        return $this->kind;
    }

    public function setKind($kind): void
    {
        $this->kind = $kind;
    }

    /**
     * @inheritdoc
     */
    public function idProperty(): string
    {
        return 'self';
    }

    public function setSelf($self): void
    {
        $this->self = $self;
    }

    /**
     * Get the parent Folder ID for the current Specstore object.
     *
     * @return mixed
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * Set the parent Folder ID for the current Specstore object.
     *
     * @param $folder
     */
    public function setFolder($folder): void
    {
        $this->folder = $folder;
    }

    /**
     * Get the permissions associated with the Specstore object.
     *
     * @return mixed
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Set the permissions associated with the Specstore object.
     *
     * @return mixed
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * Get the permissions associated with the Specstore object.
     *
     * @return mixed
     */
    public function getIsTrashed()
    {
        return $this->isTrashed;
    }

    /**
     * Get the permissions associated with the Specstore object.
     *
     * @return mixed
     */
    public function setIsTrashed($isTrashed)
    {
        $this->isTrashed = $isTrashed;
    }
}
