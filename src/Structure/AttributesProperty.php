<?php

namespace Apigee\Edge\Structure;

/**
 * Class Attributes.
 *
 * @package Apigee\Edge\Structure
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class AttributesProperty extends KeyValueMap
{
    /**
     * @inheritdoc
     */
    public function set(array $values): void
    {
        // Flatten the array returned by the Edge.
        // Example: $values = [['name' => 'foo', 'value' => 'bar'], ['name' => 'bar', 'value' => 'baz']].
        $flatten = [];
        foreach ($values as $item) {
            $flatten[$item['name']] = $item['value'];
        }
        $values = $flatten;
        parent::set($values);
    }
}
