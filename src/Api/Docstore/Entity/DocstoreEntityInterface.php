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
 * Interface DocstoreEntityInterface.
 */
interface DocstoreEntityInterface
{
    /**
     * @return string
     */
    public function getKind(): string;

    /**
     * @param string $kind
     */
    public function setKind(string $kind): void;

    /**
     * @return string
     */
    public static function idProperty(): string;

    /**
     * @return string|null
     */
    public function getSelf(): ?string;

    /**
     * @param $self
     */
    public function setSelf($self): void;

    /**
     * @return string|null
     */
    public function getFolder(): ?string;

    /**
     * @param string $folder
     */
    public function setFolder(string $folder): void;

    /**
     * @return array|null
     */
    public function getPermissions(): ?array;

    /**
     * @param array $permissions
     */
    public function setPermissions(array $permissions): void;

    /**
     * @return bool
     */
    public function getIsTrashed(): bool;

    /**
     * @param bool $isTrashed
     */
    public function setIsTrashed(bool $isTrashed): void;

    /**
     * @return string|null
     */
    public function getEtag(): ?string;

    /**
     * @param string $etag
     */
    public function setEtag(string $etag): void;

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreated(): ?\DateTimeImmutable;

    /**
     * @param \DateTimeImmutable $date
     */
    public function setCreated(\DateTimeImmutable $date): void;

    /**
     * @return \DateTimeImmutable|null
     */
    public function getModified(): ?\DateTimeImmutable;

    /**
     * @param \DateTimeImmutable $date
     */
    public function setModified(\DateTimeImmutable $date): void;
}
