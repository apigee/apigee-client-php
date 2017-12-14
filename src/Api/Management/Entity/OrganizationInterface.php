<?php

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\CommonEntityPropertiesInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Entity\Property\DisplayNamePropertyInterface;
use Apigee\Edge\Entity\Property\EnvironmentsPropertyInterface;
use Apigee\Edge\Entity\Property\NamePropertyInterface;
use Apigee\Edge\Entity\Property\PropertiesPropertyInterface;

/**
 * Interface OrganizationInterface.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface OrganizationInterface extends
    EntityInterface,
    CommonEntityPropertiesInterface,
    DisplayNamePropertyInterface,
    EnvironmentsPropertyInterface,
    NamePropertyInterface,
    PropertiesPropertyInterface
{
    /**
     * @return null|string
     */
    public function getType(): ?string;

    /**
     * @param string $type
     *
     * @throws \InvalidArgumentException
     */
    public function setType(string $type): void;

    /**
     * Returns possible values of "type" property.
     *
     * @return array
     */
    public function getTypes(): array;

    /**
     * Indicates whether the Content Persistance Service is enabled on the organization.
     *
     * @see https://community.apigee.com/questions/49310/how-many-custom-attributes-are-allowed-on-an-edge.html
     *
     * @return bool
     */
    public function isCpsEnabled(): bool;
}
