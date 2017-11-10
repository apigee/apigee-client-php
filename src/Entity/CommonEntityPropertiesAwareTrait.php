<?php

namespace Apigee\Edge\Entity;

/**
 * Trait CommonEntityPropertiesAwareTrait.
 *
 * Contains default implementation of common Apigee Edge properties and their accessors.
 * Common properties are usually auto-generated and this is the reason why those properties's setters are protected and
 * marked as internal.
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 * @see CommonEntityPropertiesInterface
 */
trait CommonEntityPropertiesAwareTrait
{
    /**
     * @var string Unix timestamp.
     */
    protected $createdAt = '';

    /**
     * @var string Email address.
     */
    protected $createdBy = '';

    /**
     * @var string Unix timestamp.
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
    protected function setCreatedAt(string $timestamp): void
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
    protected function setCreatedBy(string $email): void
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
    protected function setLastModifiedAt(string $timestamp): void
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
    protected function setLastModifiedBy(string $email): void
    {
        $this->lastModifiedBy = $email;
    }
}
