<?php

namespace Apigee\Edge\Entity;

/**
 * Trait CommonEntityPropertiesAwareTrait.
 *
 * Contains default implementation of common Apigee Edge properties and their accessors.
 * Common properties are usually auto-generated and this is the reason why those properties's setters are marked as
 * internal.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @see CommonEntityPropertiesInterface
 */
trait CommonEntityPropertiesAwareTrait
{
    /**
     * @var null|\DateTimeImmutable
     */
    protected $createdAt;

    /**
     * Email address of the organization user who created the entity.
     *
     * @var null|string
     */
    protected $createdBy;

    /**
     * @var null|\DateTimeImmutable
     */
    protected $lastModifiedAt;

    /**
     * Email address of the organization user who modified the entity last time.
     *
     * @var null|string
     */
    protected $lastModifiedBy;

    /**
     * @inheritdoc
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Set creation date of an entity from an Edge API response.
     *
     * @param \DateTimeImmutable $date
     *
     * @internal
     */
    public function setCreatedAt(\DateTimeImmutable $date): void
    {
        $this->createdAt = $date;
    }

    /**
     * @inheritdoc
     */
    public function getCreatedBy(): ?string
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
    public function getLastModifiedAt(): ?\DateTimeImmutable
    {
        return $this->lastModifiedAt;
    }

    /**
     * Set the last modification date of an entity from an Edge API response.
     *
     * @param \DateTimeImmutable $date
     *
     * @internal
     */
    public function setLastModifiedAt(\DateTimeImmutable $date): void
    {
        $this->lastModifiedAt = $date;
    }

    /**
     * @inheritdoc
     */
    public function getLastModifiedBy(): ?string
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
