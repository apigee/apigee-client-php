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

namespace Apigee\Edge\Api\Monetization\Entity;

use Apigee\Edge\Entity\Property\DescriptionPropertyInterface;
use Apigee\Edge\Entity\Property\NamePropertyInterface;

/**
 * Interface PaymentProviderInterface.
 */
interface PaymentProviderInterface extends EntityInterface, NamePropertyInterface, DescriptionPropertyInterface
{
    /**
     * @return string
     */
    public function getAuthType(): string;

    /**
     * @param string $authType
     */
    public function setAuthType(string $authType): void;

    /**
     * @return string
     */
    public function getCredential(): string;

    /**
     * @param string $credential
     */
    public function setCredential(string $credential): void;

    /**
     * @return string
     */
    public function getEndpoint(): string;

    /**
     * @param string $endpoint
     */
    public function setEndpoint(string $endpoint): void;

    /**
     * @return string
     */
    public function getMerchantCode(): string;

    /**
     * @param string $merchantCode
     */
    public function setMerchantCode(string $merchantCode): void;
}
