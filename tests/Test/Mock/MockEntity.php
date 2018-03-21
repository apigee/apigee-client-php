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

namespace Apigee\Edge\Tests\Test\Mock;

use Apigee\Edge\Api\Management\Entity\AppCredential;
use Apigee\Edge\Entity\Entity;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Structure\CredentialProduct;
use Apigee\Edge\Structure\PropertiesProperty;

/**
 * Dummy entity that we can use in ours tests.
 */
class MockEntity extends Entity
{
    /** @var null */
    private $nullable;

    /** @var bool */
    private $bool = true;

    /** @var int */
    private $int = 2;

    /** @var int */
    private $zero = 0;

    /** @var float */
    private $double = 2.2;

    /** @var string */
    private $string = 'string';

    /** @var string */
    private $emptyString = '';

    /** @var AttributesProperty */
    private $attributesProperty;

    /** @var PropertiesProperty */
    private $propertiesProperty;

    /** @var AppCredential[] */
    private $appCredential = [];

    /** @var CredentialProduct */
    private $credentialProduct;

    /** @var null|\DateTimeImmutable */
    private $date;

    /**
     * MockEntity constructor.
     *
     * @param array $values
     *
     * @throws \ReflectionException
     */
    public function __construct(array $values = [])
    {
        $this->attributesProperty = new AttributesProperty(['foo' => 'bar']);
        $this->propertiesProperty = new PropertiesProperty(['foo' => 'bar']);
        $this->credentialProduct = new CredentialProduct('foo', 'bar');
        $this->appCredential = [new AppCredential(
            [
                'apiProducts' => [$this->credentialProduct],
                'attributes' => $this->attributesProperty,
                'consumerKey' => 'consumerKey',
                'consumerSecret' => 'consumerSecret',
                'scopes' => ['foo', 'bar'],
                'status' => AppCredential::STATUS_REVOKED,
            ]
        )];
        parent::__construct($values);
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @param \DateTimeImmutable|null $date
     */
    public function setDate(?\DateTimeImmutable $date): void
    {
        $this->date = $date;
    }

    /**
     * @inheritdoc
     */
    public function idProperty(): string
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function id(): ?string
    {
        return null;
    }

    public function getNullable()
    {
        return $this->nullable;
    }

    /**
     * @param null $nullable
     */
    public function setNullable($nullable): void
    {
        $this->nullable = $nullable;
    }

    /**
     * @return bool
     */
    public function isBool(): bool
    {
        return $this->bool;
    }

    /**
     * @param bool $bool
     */
    public function setBool(bool $bool): void
    {
        $this->bool = $bool;
    }

    /**
     * @return int
     */
    public function getInt(): int
    {
        return $this->int;
    }

    /**
     * @param int $int
     */
    public function setInt(int $int): void
    {
        $this->int = $int;
    }

    /**
     * @return float
     */
    public function getDouble(): float
    {
        return $this->double;
    }

    /**
     * @param float $double
     */
    public function setDouble(float $double): void
    {
        $this->double = $double;
    }

    /**
     * @return string
     */
    public function getString(): string
    {
        return $this->string;
    }

    /**
     * @param string $string
     */
    public function setString(string $string): void
    {
        $this->string = $string;
    }

    /**
     * @return \Apigee\Edge\Structure\AttributesProperty
     */
    public function getAttributesProperty(): AttributesProperty
    {
        return $this->attributesProperty;
    }

    /**
     * @param \Apigee\Edge\Structure\AttributesProperty $attributesProperty
     */
    public function setAttributesProperty(AttributesProperty $attributesProperty
    ): void {
        $this->attributesProperty = $attributesProperty;
    }

    /**
     * @return \Apigee\Edge\Structure\PropertiesProperty
     */
    public function getPropertiesProperty(): PropertiesProperty
    {
        return $this->propertiesProperty;
    }

    /**
     * @param \Apigee\Edge\Structure\PropertiesProperty $propertiesProperty
     */
    public function setPropertiesProperty(PropertiesProperty $propertiesProperty
    ): void {
        $this->propertiesProperty = $propertiesProperty;
    }

    /**
     * @return \Apigee\Edge\Structure\CredentialProduct
     */
    public function getCredentialProduct(): CredentialProduct
    {
        return $this->credentialProduct;
    }

    /**
     * @param \Apigee\Edge\Structure\CredentialProduct $credentialProduct
     */
    public function setCredentialProduct(CredentialProduct $credentialProduct
    ): void {
        $this->credentialProduct = $credentialProduct;
    }

    /**
     * @return AppCredential[]
     */
    public function getAppCredential(): array
    {
        return $this->appCredential;
    }

    /**
     * @param AppCredential[] $appCredential
     */
    public function setAppCredential(array $appCredential): void
    {
        $this->appCredential = $appCredential;
    }

    /**
     * @return int
     */
    public function getZero(): int
    {
        return $this->zero;
    }

    /**
     * @param int $zero
     */
    public function setZero(int $zero): void
    {
        $this->zero = $zero;
    }

    /**
     * @return string
     */
    public function getEmptyString(): string
    {
        return $this->emptyString;
    }

    /**
     * @param string $emptyString
     */
    public function setEmptyString(string $emptyString): void
    {
        $this->emptyString = $emptyString;
    }
}
