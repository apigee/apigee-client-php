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
 * - An entity should not have other properties than what Edge returns for a related API call, but it could have
 *   additional helper methods that make developers life easier. @see Organization::isCpsEnabled()
 * - Entity properties with object or array types must be initialized.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
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
     * Deep clone for entity structures.
     *
     * Inspired by https://github.com/kore/DataObject/blob/master/src/Kore/DataObject/DataObject.php
     */
    public function __clone()
    {
        $ro = new \ReflectionObject($this);
        foreach ($ro->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($this);
            if (is_object($value)) {
                $this->{$property->getName()} = clone $value;
            }
            if (is_array($value)) {
                $this->cloneArray($value);
                $this->{$property->getName()} = $value;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function id(): ?string
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
     * Deep clone for arrays.
     *
     * @param array $array
     */
    private function cloneArray(array &$array): void
    {
        foreach ($array as $key => $value) {
            if (is_object($value)) {
                $array[$key] = clone $value;
            }
            if (is_array($value)) {
                $this->cloneArray($array[$key]);
            }
        }
    }
}
