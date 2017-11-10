<?php

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\CommonEntityPropertiesInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Entity\Property\AppsPropertyInterface;
use Apigee\Edge\Entity\Property\AttributesPropertyInterface;
use Apigee\Edge\Entity\Property\CompaniesPropertyInterface;
use Apigee\Edge\Entity\Property\OrganizationNamePropertyInterface;
use Apigee\Edge\Entity\Property\OrganizationPropertyInterface;
use Apigee\Edge\Entity\Property\StatusPropertyInterface;

/**
 * Interface DeveloperInterface.
 *
 * @package Apigee\Edge\Api\Management\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface DeveloperInterface extends
    EntityInterface,
    CommonEntityPropertiesInterface,
    OrganizationNamePropertyInterface,
    AppsPropertyInterface,
    CompaniesPropertyInterface,
    StatusPropertyInterface,
    AttributesPropertyInterface
{
    /**
     * @return array Names of apps that this developer owns.
     */
    public function getApps(): array;

    /**
     * @param string $appName Name of app.
     * @return bool
     */
    public function hasApp(string $appName): bool;

    /**
     * @return array Name of companies that this developer is a member.
     */
    public function getCompanies(): array;

    /**
     * @param string $companyName Name of company.
     * @return bool
     */
    public function hasCompany(string $companyName): bool;

    /**
     * @return string UUID of the developer entity.
     */
    public function getDeveloperId(): string;

    /**
     * @return string
     */
    public function getUserName(): string;

    /**
     * @param string $userName
     */
    public function setUserName(string $userName): void;

    /**
     * @return string Email address of developer.
     */
    public function getEmail(): string;

    /**
     * @param string $email Email address of developer.
     */
    public function setEmail(string $email): void;

    /**
     * @return string
     */
    public function getFirstName(): string;

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void;

    /**
     * @return string
     */
    public function getLastName(): string;

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void;
}
