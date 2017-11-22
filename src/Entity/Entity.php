<?php

namespace Apigee\Edge\Entity;

use Apigee\Edge\Api\Management\Entity\Organization;

/**
 * Base representation of an Edge entity.
 *
 * Common properties and methods that are available on all Apigee Edge entity.
 *
 * Rules:
 * - Name of an entity property should be the same as the one in the Edge response.
 * - Entity properties should not be public, but they should have public getters and setters.
 * - An entity should not have other properties than what Edge's returns for an API call, but it could have additional
 *   helper methods that make developers life easier. @see Organization::isCpsEnabled()
 * - Entity properties with object or array types must be initialized.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @package Apigee\Edge\Entity
 */
class Entity implements EntityInterface
{
    /**
     * On the majority of entities this property is the primary entity.
     */
    private const DEFAULT_ID_FIELD = 'name';

    /**
     * Entity constructor.
     *
     * @param array $values
     *   Associative array with entity properties and their values.
     */
    public function __construct(array $values = [])
    {
        $ro = new \ReflectionObject($this);
        foreach ($ro->getProperties() as $property) {
            if (!array_key_exists($property->getName(), $values)) {
                continue;
            }
            $setter = 'set' . ucfirst($property->getName());
            if ($ro->hasMethod($setter)) {
                $value = $values[$property->getName()];
                $rm = new \ReflectionMethod($this, $setter);
                $rm->invoke($this, $value);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function id(): string
    {
        return $this->{$this->idProperty()};
    }

    /**
     * @inheritdoc
     */
    public function idProperty(): string
    {
        return self::DEFAULT_ID_FIELD;
    }

    /**
     * @var string Unix epoch time in milliseconds.
     */
    protected $createdAt = '';

    /**
     * @var string Email address.
     */
    protected $createdBy = '';

    /**
     * @var string Unix epoch time in milliseconds.
     */
    protected $lastModifiedAt = '';

    /**
     * @var string Email address.
     */
    protected $lastModifiedBy = '';

    /**
     * @inheritdoc
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * Set creation date of an entity from an Edge API response.
     *
     * @param string $timestamp
     *   Unix timestamp.
     *
     * @internal
     */
    public function setCreatedAt(string $timestamp): void
    {
        $this->createdAt = $timestamp;
    }

    /**
     * @inheritdoc
     */
    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }

    /**
     * Set creator of this entity from an Edge API response.
     *
     * @param string $email
     *   User/developer mail.
     *
     * @internal
     */
    public function setCreatedBy(string $email): void
    {
        $this->createdBy = $email;
    }

    /**
     * @inheritdoc
     */
    public function getLastModifiedAt(): string
    {
        return $this->lastModifiedAt;
    }

    /**
     * Set the last modification date of an entity from an Edge API response.
     *
     * @param string $timestamp
     *   Unix timestamp.
     *
     * @internal
     */
    public function setLastModifiedAt(string $timestamp): void
    {
        $this->lastModifiedAt = $timestamp;
    }

    /**
     * @inheritdoc
     */
    public function getLastModifiedBy(): string
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the last modifier of this entity from an Edge API response.
     *
     * @param string $email
     *   User/developer mail.
     *
     * @internal
     */
    public function setLastModifiedBy(string $email): void
    {
        $this->lastModifiedBy = $email;
    }
}
