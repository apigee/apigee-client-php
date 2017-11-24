<?php

namespace Apigee\Edge\Entity\Property;

use Apigee\Edge\Structure\PropertiesProperty;

/**
 * Trait PropertiesPropertyAwareTrait.
 *
 * @package Apigee\Edge\Entity\Property
 * @author Dezső Biczó <mxr576@gmail.com>
 */
trait PropertiesPropertyAwareTrait
{
    /** @var \Apigee\Edge\Structure\PropertiesProperty */
    protected $properties;

    /**
     * @inheritdoc
     */
    public function getProperties(): array
    {
        return $this->properties->values();
    }

    /**
     * @inheritdoc
     */
    public function setProperties(PropertiesProperty $properties): void
    {
        $this->properties = $properties;
    }

    /**
     * @inheritdoc
     */
    public function getPropertyValue(string $property): ?string
    {
        return $this->properties->getValue($property);
    }

    /**
     * @inheritdoc
     */
    public function addProperty(string $name, string $value): void
    {
        $this->properties->add($name, $value);
    }

    /**
     * @inheritdoc
     */
    public function hasProperty(string $name): bool
    {
        return $this->properties->has($name);
    }

    /**
     * @inheritdoc
     */
    public function deleteProperty(string $name): void
    {
        $this->properties->delete($name);
    }
}
