<?php

namespace Apigee\Edge\Tests\Test\Mock;

use Apigee\Edge\Api\Management\Entity\AppCredential;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Structure\CredentialProduct;
use Apigee\Edge\Structure\PropertiesProperty;

/**
 * Dummy EntityInterface implementation that we can use in ours tests.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class Entity implements EntityInterface
{
    /** @var null */
    protected $nullable;

    /** @var bool */
    protected $bool = true;

    /** @var int */
    protected $int = 2;

    /** @var float */
    protected $double = 2.2;

    /** @var string */
    protected $string = 'string';

    /** @var AttributesProperty */
    protected $attributesProperty;

    /** @var PropertiesProperty */
    protected $propertiesProperty;

    /** @var AppCredential[] */
    protected $appCredential = [];

    /** @var CredentialProduct */
    protected $credentialProduct;

    /**
     * Entity constructor.
     */
    public function __construct()
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
                'issuedAt' => 'issuedAt',
                'expiresAt' => 'expiresAt',
                'scopes' => ['foo', 'bar'],
                'status' => AppCredential::STATUS_REVOKED,
            ]
        )];
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
}
