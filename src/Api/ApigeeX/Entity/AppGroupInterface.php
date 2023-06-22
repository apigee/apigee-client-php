<?php

/*
 * Copyright 2023 Google LLC
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

namespace Apigee\Edge\Api\ApigeeX\Entity;

use Apigee\Edge\Entity\CommonEntityPropertiesInterface;
use Apigee\Edge\Entity\Property\AttributesPropertyInterface;
use Apigee\Edge\Entity\Property\DisplayNamePropertyInterface;
use Apigee\Edge\Entity\Property\NamePropertyInterface;
use Apigee\Edge\Entity\Property\StatusPropertyInterface;

/**
 * Interface AppGroupInterface.
 */
interface AppGroupInterface extends AttributesPropertyInterface,
    DisplayNamePropertyInterface,
    NamePropertyInterface,
    StatusPropertyInterface,
    CommonEntityPropertiesInterface
{
    /**
     * @param string $channelUri
     */
    public function setChannelUri(string $channelUri): void;

    /**
     * @return string|null
     */
    public function getChannelUri(): ?string;

    /**
     * @param string $channelId
     */
    public function setChannelId(string $channelId): void;

    /**
     * @return string|null
     */
    public function getChannelId(): ?string;
}
