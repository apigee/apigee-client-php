<?php

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\Entity;
use Apigee\Edge\Entity\Property\AttributesPropertyAwareTrait;
use Apigee\Edge\Entity\Property\ScopesPropertyAwareTrait;
use Apigee\Edge\Entity\Property\StatusPropertyAwareTrait;

/**
 * Class Credential.
 *
 * @package Apigee\Edge\Api\Management\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class Credential extends Entity implements CredentialInterface
{
    use AttributesPropertyAwareTrait;
    use ScopesPropertyAwareTrait;
    use StatusPropertyAwareTrait;

    /** @var \Apigee\Edge\Structure\CredentialProduct[] */
    protected $apiProducts = [];

    /** @var string */
    protected $consumerKey;

    /** @var string */
    protected $consumerSecret;

    /** @var string Unix epoch time, but it can be -1. */
    protected $expiresAt;

    /** @var string Unix epoch time. */
    protected $issuedAt;

    /**
     * @inheritdoc
     */
    public function idProperty(): string
    {
        return 'consumerKey';
    }

    /**
     * Get list of API products included in this credential with their statuses.
     *
     * @return \Apigee\Edge\Structure\CredentialProduct[]
     */
    public function getApiProducts(): array
    {
        return $this->apiProducts;
    }

    /**
     * @param \Apigee\Edge\Structure\CredentialProduct[] $apiProducts
     *
     * @internal
     */
    public function setApiProducts(array $apiProducts)
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
     * @param string $consumerKey
     *
     * @internal
     */
    public function setConsumerKey(string $consumerKey)
    {
        $this->consumerKey = $consumerKey;
    }

    /**
     * @return string
     */
    public function getConsumerSecret(): string
    {
        return $this->consumerSecret;
    }

    /**
     * @param string $consumerSecret
     */
    public function setConsumerSecret(string $consumerSecret)
    {
        $this->consumerSecret = $consumerSecret;
    }

    /**
     * @return mixed
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @param mixed $expiresAt
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return string
     */
    public function getIssuedAt(): string
    {
        return $this->issuedAt;
    }

    /**
     * @param string $issuedAt
     *
     * @internal
     */
    public function setIssuedAt(string $issuedAt)
    {
        $this->issuedAt = $issuedAt;
    }
}
