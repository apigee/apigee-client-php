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

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\CommonEntityPropertiesInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Entity\Property\AttributesPropertyInterface;
use Apigee\Edge\Entity\Property\DescriptionPropertyInterface;
use Apigee\Edge\Entity\Property\DisplayNamePropertyInterface;
use Apigee\Edge\Entity\Property\NamePropertyInterface;
use Apigee\Edge\Entity\Property\ScopesPropertyInterface;
use Apigee\Edge\Entity\Property\StatusPropertyInterface;

/**
 * Interface AppInterface.
 */
interface AppInterface extends
    EntityInterface,
    AttributesPropertyInterface,
    CommonEntityPropertiesInterface,
    DescriptionPropertyInterface,
    DisplayNamePropertyInterface,
    NamePropertyInterface,
    ScopesPropertyInterface,
    StatusPropertyInterface
{
    public const STATUS_APPROVED = 'approved';

    public const STATUS_REVOKED = 'revoked';

    /**
     * Get OAuth scopes.
     *
     * Scopes of app can not be modified on the entity level therefore we could not extend the ScopesPropertyInterface
     * here.
     *
     * @return string[]
     */
    public function getScopes(): array;

    /**
     * @return string
     */
    public function getAppFamily(): string;

    /**
     * @param string $appFamily
     */
    public function setAppFamily(string $appFamily): void;

    /**
     * @return string
     */
    public function getAppId(): ?string;

    /**
     * @return string
     */
    public function getCallbackUrl(): ?string;

    /**
     * @param string $callbackUrl
     */
    public function setCallbackUrl(string $callbackUrl): void;

    /**
     * @return \Apigee\Edge\Api\Management\Entity\AppCredentialInterface[]
     */
    public function getCredentials(): array;
}
