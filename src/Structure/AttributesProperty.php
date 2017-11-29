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
     * AttributesProperty constructor.
     *
     * @param array $values
     *   Ex.: [['name' => 'value'], ['foo' => 'bar']]
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);
    }
}
