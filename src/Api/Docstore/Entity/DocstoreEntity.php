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

use Apigee\Edge\Entity\Entity;
use Apigee\Edge\Entity\Property\DescriptionPropertyAwareTrait;
use Apigee\Edge\Entity\Property\NamePropertyAwareTrait;

/**
 * Abstract object to represent the Docstore entities.
 */
abstract class DocstoreEntity extends Entity implements DocstoreEntityInterface
{
    use NamePropertyAwareTrait;
    use DescriptionPropertyAwareTrait;

    /**
     * @var array
     */
    protected $permissions;
    /**
     * @var bool
     */
    protected $isTrashed = false;
    /**
     * @var string
     */
    protected $self;
    /**
     * @var string
     */
    protected $folder;

    /**
     * @var string
     */
    protected $kind;

    /**
     * @var string
     */
    protected $etag;

    protected $created;
    protected $modified;

    /**
     * @return string
     */
    public function getKind(): string
    {
        return $this->kind;
    }

    /**
     * @param string $kind
     */
    public function setKind(string $kind): void
    {
        $this->kind = $kind;
    }

    /**
     * @inheritdoc
     */
    public static function idProperty(): string
    {
        return 'self';
    }

    /**
     * @return string|null
     */
    public function getSelf(): ?string
    {
        return $this->self;
    }

    /**
     * @param $self
     */
    public function setSelf($self): void
    {
        $this->self = $self;
    }

    /**
     * Get the parent Folder ID for the current Docstore object.
     *
     * @return string|null
     */
    public function getFolder(): ?string
    {
        return $this->folder;
    }

    /**
     * Set the parent Folder ID for the current Docstore object.
     *
     * @param $folder
     */
    public function setFolder(string $folder): void
    {
        $this->folder = $folder;
    }

    /**
     * Get the permissions associated with the Docstore object.
     *
     * @return array|null
     */
    public function getPermissions(): ?array
    {
        return $this->permissions;
    }

    /**
     * Set the permissions associated with the Docstore object.
     */
    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
    }

    /**
     * Is the Docstore object trashed.
     *
     * @return bool
     */
    public function getIsTrashed(): bool
    {
        return $this->isTrashed;
    }

    /**
     * Set the flag to indicate the Docstore object is trashed.
     */
    public function setIsTrashed(bool $isTrashed): void
    {
        $this->isTrashed = $isTrashed;
    }

    /**
     * @return string|null
     */
    public function getEtag(): ?string
    {
        return $this->etag;
    }

    /**
     * @param $etag
     */
    public function setEtag(string $etag): void
    {
        $this->etag = $etag;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreated(): ?\DateTimeImmutable
    {
        return $this->created;
    }

    /**
     * @param \DateTimeImmutable $date
     *
     * @internal
     */
    public function setCreated(\DateTimeImmutable $date): void
    {
        $this->created = $date;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getModified(): ?\DateTimeImmutable
    {
        return $this->modified;
    }

    /**
     * @param \DateTimeImmutable $date
     *
     * @internal
     */
    public function setModified(\DateTimeImmutable $date): void
    {
        $this->modified = $date;
    }
}
