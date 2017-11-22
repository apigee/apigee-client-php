<?php

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\Entity;
use Apigee\Edge\Entity\Property\AttributesPropertyAwareTrait;
use Apigee\Edge\Entity\Property\DescriptionPropertyAwareTrait;
use Apigee\Edge\Entity\Property\DisplayNamePropertyAwareTrait;
use Apigee\Edge\Entity\Property\EnvironmentsPropertyAwareTrait;
use Apigee\Edge\Entity\Property\NamePropertyAwareTrait;
use Apigee\Edge\Structure\AttributesProperty;

/**
 * Describes an API product entity.
 *
 * @package Apigee\Edge\Api\Management\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class ApiProduct extends Entity implements ApiProductInterface
{
    use AttributesPropertyAwareTrait;
    use DescriptionPropertyAwareTrait;
    use DisplayNamePropertyAwareTrait;
    use EnvironmentsPropertyAwareTrait;
    use NamePropertyAwareTrait;

    public const APPROVAL_TYPE_AUTO = 'auto';

    public const APPROVAL_TYPE_MANUAL = 'manual';

    public const QUOTA_INTERVAL_MINUTE = 'minute';

    public const QUOTA_INTERVAL_HOUR = 'hour';

    public const QUOTA_INTERVAL_DAY = 'day';

    public const QUOTA_INTERVAL_MONTH = 'month';

    /** @var string */
    protected $approvalType;

    /** @var string[] */
    protected $apiResources = [];

    /** @var string[] */
    protected $proxies = [];

    /** @var int */
    protected $quota;

    /** @var int */
    protected $quotaInterval;

    /** @var string */
    protected $quotaTimeUnit;

    /**
     * ApiProduct constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        $this->attributes = new AttributesProperty();
        parent::__construct($values);
    }

    /**
     * @inheritdoc
     */
    public function getProxies(): array
    {
        return $this->proxies;
    }

    /**
     * @inheritdoc
     */
    public function setProxies(array $proxies)
    {
        $this->proxies = $proxies;
    }

    /**
     * @inheritdoc
     */
    public function getQuota(): ?int
    {
        return $this->quota;
    }

    /**
     * @inheritdoc
     */
    public function setQuota(int $quota)
    {
        $this->quota = $quota;
    }

    /**
     * @inheritdoc
     */
    public function getQuotaInterval(): ?int
    {
        return $this->quotaInterval;
    }

    /**
     * @inheritdoc
     */
    public function setQuotaInterval(int $quotaInterval)
    {
        $this->quotaInterval = $quotaInterval;
    }

    /**
     * @inheritdoc
     */
    public function getQuotaTimeUnit(): ?string
    {
        return $this->quotaTimeUnit;
    }

    /**
     * @inheritdoc
     */
    public function setQuotaTimeUnit(string $quotaTimeUnit)
    {
        $this->quotaTimeUnit = $quotaTimeUnit;
    }

    /**
     * @inheritdoc
     */
    public function getApprovalType(): string
    {
        return $this->approvalType;
    }

    /**
     * @inheritdoc
     */
    public function setApprovalType(string $approvalType)
    {
        $this->approvalType = $approvalType;
    }

    /**
     * @inheritdoc
     */
    public function getApiResources(): array
    {
        return $this->apiResources;
    }

    /**
     * @inheritdoc
     */
    public function setApiResources(array $apiResources)
    {
        $this->apiResources = $apiResources;
    }
}
