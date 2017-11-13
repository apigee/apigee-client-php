<?php

namespace Apigee\Edge\Structure;

/**
 * Class PropertiesProperty.
 *
 * @package Apigee\Edge\Structure
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class PropertiesProperty extends KeyValueMap
{
    /**
     * @inheritdoc
     */
    public function __construct(array $values = [])
    {
        $values = $this->transform($values);
        parent::__construct($values);
    }

    /**
     * @inheritdoc
     */
    public function set(array $values): void
    {
        $values = $this->transform($values);
        parent::set($values);
    }

    /**
     * Flatten the array returned by the Edge.
     *
     * Example: $values = ['property' => [['name' => 'foo', 'value' => 'bar']]].
     *
     * @param array $values
     *
     * @return array
     */
    protected function transform(array $values): array
    {
        if (array_key_exists('property', $values) && is_array($values['property'])) {
            $flatten = [];
            foreach ($values['property'] as $value) {
                $flatten[$value['name']] = $value['value'];
            }
            $values = $flatten;
        }
        return $values;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        // Transform JSON representation of properties to compatible with Edge.
        $return = [
            'property' => parent::jsonSerialize(),
        ];
        return $return;
    }
}
