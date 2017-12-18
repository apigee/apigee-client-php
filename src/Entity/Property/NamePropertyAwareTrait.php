<?php

namespace Apigee\Edge\Entity\Property;

/**
 * Trait NamePropertyAwareTrait.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
trait NamePropertyAwareTrait
{
    /** @var string */
    protected $name;

    /**
     * @inheritdoc
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the name of this entity from an Edge API response.
     *
     * The name of an entity can not be changed by modifying the value of this property because it is a primary key
     * when it is available.
     *
     * @param string $name
     *   Name of the entity.
     *
     * @internal
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
