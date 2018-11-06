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

use Apigee\Edge\Entity\Entity;
use Apigee\Edge\Entity\Property\AttributesPropertyAwareTrait;
use Apigee\Edge\Entity\Property\ScopesPropertyAwareTrait;
use Apigee\Edge\Entity\Property\StatusPropertyAwareTrait;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Structure\CredentialProductInterface;

/**
 * Class AppCredential.
 */
class AppCredential extends Entity implements AppCredentialInterface
{
    use AttributesPropertyAwareTrait;
    use ScopesPropertyAwareTrait;
    use StatusPropertyAwareTrait;

    /**
     * Array of credential products or an empty array.
     *
     * @var \Apigee\Edge\Structure\CredentialProduct[]|array
     */
    protected $apiProducts = [];

    /** @var string */
    protected $consumerKey;

    /** @var string */
    protected $consumerSecret;

    /**
     * Default value is null, which is equivalent of "-1" from the raw API response that means the credential
     * never expires. So if this value is null then it either means that this is a new entity (check whether consumerKey
     * or consumerSecret are also null) or this credential never expires.
     *
     * @var null|\DateTimeImmutable
     */
    protected $expiresAt;

    /** @var \DateTimeImmutable */
    protected $issuedAt;

    /**
     * AppCredential constructor.
     *
     * @param array $values
     *
     * @throws \ReflectionException
     */
    public function __construct(array $values = [])
    {
        $this->attributes = new AttributesProperty();
        parent::__construct($values);
    }

    /**
     * @inheritdoc
     */
    public function idProperty(): string
    {
        return 'consumerKey';
    }

    /**
     * @inheritdoc
     */
    public function getApiProducts(): array
    {
        return $this->apiProducts;
    }

    /**
     * Set api products included in an app credential from an Edge API response.
     *
     * Included API products in an app credential can not be changed by modifying this property's value.
     *
     * @param \Apigee\Edge\Structure\CredentialProductInterface ...$apiProducts
     *
     * @internal
     */
    public function setApiProducts(CredentialProductInterface ...$apiProducts): void
    {
        $this->apiProducts = $apiProducts;
    }

    /**
     * @return string
     */
    public function getConsumerKey(): string
    {
        return $this->consumerKey;
    }

    /**
     * Set consumer key of an app credential from an Edge API response.
     *
     * Consumer key can not be changed by modifying this property's value.
     *
     * @param string $consumerKey
     *
     * @internal
     */
    public function setConsumerKey(string $consumerKey): void
    {
        $this->consumerKey = $consumerKey;
    }

    /**
     * @inheritdoc
     */
    public function getConsumerSecret(): string
    {
        return $this->consumerSecret;
    }

    /**
     * Set consumer secret of an app credential from an Edge API response.
     *
     * Consumer secret can not be changed by modifying this property's value.
     *
     * @param string $consumerSecret
     *
     * @internal
     */
    public function setConsumerSecret(string $consumerSecret): void
    {
        $this->consumerSecret = $consumerSecret;
    }

    /**
     * @inheritdoc
     */
    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    /**
     * Set expiration date of an app credential from an Edge API response.
     *
     * Expiration date can not be changed by modifying this property's value.
     *
     * @param null|\DateTimeImmutable $date
     *
     * @internal
     */
    public function setExpiresAt($date): void
    {
        $this->expiresAt = $date;
    }

    /**
     * @inheritdoc
     */
    public function getIssuedAt(): ?\DateTimeImmutable
    {
        return $this->issuedAt;
    }

    /**
     * Set issued date of an app credential from an Edge API response.
     *
     * Consumer key can not be changed by modifying this property's value.
     *
     * @param \DateTimeImmutable $date
     *
     * @internal
     */
    public function setIssuedAt(\DateTimeImmutable $date): void
    {
        $this->issuedAt = $date;
    }
}
