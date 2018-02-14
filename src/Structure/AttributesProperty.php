<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Structure;

/**
 * Class Attributes.
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
