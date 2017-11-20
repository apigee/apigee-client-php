<?php

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\Entity;
use Apigee\Edge\Entity\Property\AttributesPropertyAwareTrait;
use Apigee\Edge\Entity\Property\OrganizationNamePropertyAwareTrait;
use Apigee\Edge\Entity\Property\OrganizationPropertyAwareTrait;
use Apigee\Edge\Entity\Property\StatusPropertyAwareTrait;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Structure\KeyValueMap;

/**
 * Class Developer.
 *
 * @package Apigee\Edge\Api\Management\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class Developer extends Entity implements DeveloperInterface
{
    use OrganizationNamePropertyAwareTrait;
    use StatusPropertyAwareTrait;
    use AttributesPropertyAwareTrait;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    /** @var string UUID of the developer entity. */
    protected $developerId = '';

    /** @var string */
    protected $userName = '';

    /** @var string */
    protected $email = '';

    /** @var string */
    protected $firstName = '';

    /** @var string */
    protected $lastName = '';

    /** @var KeyValueMap */
    protected $apps;

    /** @var KeyValueMap */
    protected $companies;

    /**
     * Developer constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        $this->apps = new KeyValueMap();
        $this->companies = new KeyValueMap();
        $this->attributes = new AttributesProperty();
        parent::__construct($values);
    }

    /**
     * @inheritdoc
     */
    public function idProperty(): string
    {
        // The developerId is also a primary key of a developer entity. Other entities are usually
        // referencing to a developer with email address (except developer apps) but we should not use it
        // as a primary key, because email address can be changed and without a workaround it would cause problems when
        // \Apigee\Edge\Entity\BaseEntityControllerInterface::save() gets called.
        return 'developerId';
    }

    /**
     * @inheritdoc
     */
    public function getApps(): array
    {
        return $this->apps->values();
    }

    /**
     * @inheritdoc
     */
    public function hasApp(string $appName): bool
    {
        return in_array($appName, $this->apps->values());
    }

    /**
     * Set app names from an Edge API response.
     *
     * Apps of a developer can not be changed by modifying this property's value.
     *
     * @param KeyValueMap $apps
     *
     * @internal
     */
    public function setApps(KeyValueMap $apps): void
    {
        $this->apps = $apps;
    }

    /**
     * @inheritdoc
     */
    public function getCompanies(): array
    {
        return $this->companies->values();
    }

    /**
     * Set company names from an Edge API response.
     *
     * Company memberships of a developer can not be changed by modifying this property's value.
     *
     * @param KeyValueMap $companies
     *
     * @internal
     */
    public function setCompanies(KeyValueMap $companies): void
    {
        $this->companies = $companies;
    }

    /**
     * @inheritdoc
     */
    public function hasCompany(string $companyName): bool
    {
        return in_array($companyName, $this->companies->values());
    }

    /**
     * @inheritdoc
     */
    public function getDeveloperId(): string
    {
        return $this->developerId;
    }

    /**
     * Set developer id from an Edge API response.
     *
     * @param string $developerId UUID.
     *
     * @internal
     */
    public function setDeveloperId(string $developerId): void
    {
        $this->developerId = $developerId;
    }

    /**
     * @inheritdoc
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @inheritdoc
     */
    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    /**
     * @inheritdoc
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @inheritdoc
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @inheritdoc
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @inheritdoc
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @inheritdoc
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @inheritdoc
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }
}
