<?php

/*
 * Copyright 2018 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\CommonEntityPropertiesAwareTrait;
use Apigee\Edge\Entity\Entity;
use Apigee\Edge\Entity\Property\NamePropertyAwareTrait;
use Apigee\Edge\Entity\Property\PropertiesPropertyAwareTrait;
use Apigee\Edge\Structure\PropertiesProperty;
use ReflectionException;

/**
 * Class Environment.
 *
 * @TODO
 * "mdc" property is ignored.
 */
class Environment extends Entity implements EnvironmentInterface
{
    use CommonEntityPropertiesAwareTrait;
    use NamePropertyAwareTrait;
    use PropertiesPropertyAwareTrait;

    /**
     * Environment constructor.
     *
     * @param array $values
     *
     * @throws ReflectionException
     */
    public function __construct(array $values = [])
    {
        $this->properties = new PropertiesProperty();
        parent::__construct($values);
    }
}
