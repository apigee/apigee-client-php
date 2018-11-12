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

use Apigee\Edge\Entity\Property\DescriptionPropertyAwareTrait;
use Apigee\Edge\Entity\Property\NamePropertyAwareTrait;

class PaymentProvider extends Entity implements PaymentProviderInterface
{
    use DescriptionPropertyAwareTrait;
    use NamePropertyAwareTrait;

    /** @var string */
    protected $authType;

    /** @var string */
    protected $credential;

    /** @var string */
    protected $endpoint;

    /** @var string */
    protected $merchantCode;

    /**
     * @inheritdoc
     */
    public function getAuthType(): string
    {
        return $this->authType;
    }

    /**
     * @inheritdoc
     */
    public function setAuthType(string $authType): void
    {
        $this->authType = $authType;
    }

    /**
     * @inheritdoc
     */
    public function getCredential(): string
    {
        return $this->credential;
    }

    /**
     * @inheritdoc
     */
    public function setCredential(string $credential): void
    {
        $this->credential = $credential;
    }

    /**
     * @inheritdoc
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @inheritdoc
     */
    public function setEndpoint(string $endpoint): void
    {
        $this->endpoint = $endpoint;
    }

    /**
     * @inheritdoc
     */
    public function getMerchantCode(): string
    {
        return $this->merchantCode;
    }

    /**
     * @inheritdoc
     */
    public function setMerchantCode(string $merchantCode): void
    {
        $this->merchantCode = $merchantCode;
    }
}
