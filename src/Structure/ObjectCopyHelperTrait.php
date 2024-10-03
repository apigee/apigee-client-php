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

namespace Apigee\Edge\Structure;

use ReflectionObject;

trait ObjectCopyHelperTrait
{
    /**
     * Deep clone for objects.
     *
     * Inspired by https://github.com/kore/DataObject/blob/master/src/Kore/DataObject/DataObject.php
     */
    public function __clone()
    {
        $ro = new ReflectionObject($this);
        foreach ($ro->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($this);
            if (is_object($value)) {
                $property->setValue($this, clone $value);
            }
            if (is_array($value)) {
                $this->cloneArray($value);
                $property->setValue($this, $value);
            }
        }
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
