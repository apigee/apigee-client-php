<?php

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\Entity;
use Apigee\Edge\Entity\Property\AttributesPropertyAwareTrait;
use Apigee\Edge\Entity\Property\ScopesPropertyAwareTrait;
use Apigee\Edge\Entity\Property\StatusPropertyAwareTrait;
use Apigee\Edge\Structure\AttributesProperty;

/**
 * Class AppCredential.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class AppCredential extends Entity implements AppCredentialInterface
{
    use AttributesPropertyAwareTrait;
    use ScopesPropertyAwareTrait;
    use StatusPropertyAwareTrait;

    /**
     * Status of an approved app credential returned by Edge.
     *
     * The status that you should send to the API to change status of an app credential is in the controller!
     *
     * @see \Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialController.
     */
    public const STATUS_APPROVED = 'approved';

    /**
     * Status of a revoked app credential returned by Edge.
     *
     * The status that you should send to the API to change status of an app credential is in the controller!
     *
     * @see \Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialController.
     */
    public const STATUS_REVOKED = 'revoked';

    /** @var \Apigee\Edge\Structure\CredentialProduct[] */
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
     * @var \DateTimeImmutable
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
     * Get list of API products included in this credential with their statuses.
     *
     * @return \Apigee\Edge\Structure\CredentialProduct[]
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
     * @param \Apigee\Edge\Structure\CredentialProduct[] $apiProducts
     *
     * @internal
     */
    public function setApiProducts(array $apiProducts): void
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
     * @param \DateTimeImmutable $date
     *
     * @internal
     */
    public function setExpiresAt(\DateTimeImmutable $date): void
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
