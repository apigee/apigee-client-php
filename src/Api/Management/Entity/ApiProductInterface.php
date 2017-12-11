<?php

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\CommonEntityPropertiesInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Entity\Property\AttributesPropertyInterface;
use Apigee\Edge\Entity\Property\DescriptionPropertyInterface;
use Apigee\Edge\Entity\Property\DisplayNamePropertyInterface;
use Apigee\Edge\Entity\Property\EnvironmentsPropertyInterface;
use Apigee\Edge\Entity\Property\NamePropertyInterface;
use Apigee\Edge\Entity\Property\ScopesPropertyInterface;

/**
 * Interface ApiProductInterface.
 *
 * @package Apigee\Edge\Api\Management\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface ApiProductInterface extends
    EntityInterface,
    AttributesPropertyInterface,
    CommonEntityPropertiesInterface,
    DescriptionPropertyInterface,
    DisplayNamePropertyInterface,
    EnvironmentsPropertyInterface,
    NamePropertyInterface,
    ScopesPropertyInterface
{
    /**
     * @return string[]
     */
    public function getProxies(): array;

    /**
     * @param string[] $proxy
     */
    public function setProxies(array $proxy);

    /**
     * @return int|null
     */
    public function getQuota(): ?int;

    /**
     * @param int|null $quota
     */
    public function setQuota(int $quota);

    /**
     * @return int|null
     */
    public function getQuotaInterval(): ?int;

    /**
     * @param int $quotaInterval
     */
    public function setQuotaInterval(int $quotaInterval);

    /**
     * @return string
     */
    public function getQuotaTimeUnit(): ?string;

    /**
     * @param string $quotaTimeUnit
     */
    public function setQuotaTimeUnit(string $quotaTimeUnit);

    /**
     * @return string
     */
    public function getApprovalType(): string;

    /**
     * @param string $approvalType
     */
    public function setApprovalType(string $approvalType);

    /**
     * @return string[]
     */
    public function getApiResources(): array;

    /**
     * @param string[] $apiResources
     */
    public function setApiResources(array $apiResources);
}
