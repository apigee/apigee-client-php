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
 * - Entity properties must be initialized with their default empty value. Properties with scalar types are in place
 *   others are in the constructor of an entity.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @package Apigee\Edge\Entity
 */
class Entity implements EntityInterface
{
    use CommonEntityPropertiesAwareTrait;

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
                $params = $rm->getParameters();
                /** @var \ReflectionParameter $firstParam */
                $firstParam = reset($params);
                // Call the constructors of non-scalar type properties.
                if (!$firstParam->getType()->isBuiltin()) {
                    $value = $firstParam->getClass()->newInstance($value);
                }
                // Ensure that inherited protected methods can be called.
                $rm->setAccessible(true);
                $rm->invoke($this, $value);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public static function create(array $values): EntityInterface
    {
        return new static($values);
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
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        $json = [];
        $ro = new \ReflectionObject($this);
        foreach ($ro->getProperties() as $property) {
            $getter = 'get' . ucfirst($property->getName());
            if ($ro->hasMethod($getter)) {
                $json[$property->getName()] = $this->{$getter}();
            }
        }
        // Exclude empty values from the output, even if PATCH is not supported on Apigee Edge
        // sending a smaller portion of data in POST/PUT is always a good practice.
        $json = array_filter($json);
        ksort($json);
        return (object)$json;
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return json_encode($this, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
