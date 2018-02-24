<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Utility;

final class ObjectPropertyExtractor
{
    /**
     * // From objectNormalizer.
     */
    public function extractAttributes($object)
    {
        // If not using groups, detect manually
        $attributes = [];

        $attributes += $this->extractAttributesFromMethods($object);
        $attributes += $this->extractAttributesFromProperties($object);

        return array_keys($attributes);
    }

    /**
     * @param $object
     *
     * @throws \ReflectionException
     *
     * @return array
     */
    private function extractAttributesFromMethods($object)
    {
        // methods
        $reflClass = new \ReflectionClass($object);
        $attributes = [];
        foreach ($reflClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflMethod) {
            if (0 !== $reflMethod->getNumberOfRequiredParameters() || $reflMethod->isStatic() || $reflMethod->isConstructor() || $reflMethod->isDestructor()) {
                continue;
            }

            $name = $reflMethod->name;
            $attributeName = null;

            if (0 === strpos($name, 'get') || 0 === strpos($name, 'has')) {
                // getters and hassers
                $attributeName = substr($name, 3);

                if (!$reflClass->hasProperty($attributeName)) {
                    $attributeName = lcfirst($attributeName);
                }
            } elseif (0 === strpos($name, 'is')) {
                // issers
                $attributeName = substr($name, 2);

                if (!$reflClass->hasProperty($attributeName)) {
                    $attributeName = lcfirst($attributeName);
                }
            }

            if (null !== $attributeName) {
                $attributes[$attributeName] = true;
            }
        }

        return $attributes;
    }

    /**
     * @param $object
     *
     * @throws \ReflectionException
     *
     * @return array
     */
    private function extractAttributesFromProperties($object)
    {
        $reflClass = new \ReflectionClass($object);
        $attributes = [];
        // properties
        foreach ($reflClass->getProperties(\ReflectionProperty::IS_PUBLIC) as $reflProperty) {
            if ($reflProperty->isStatic()) {
                continue;
            }

            $attributes[$reflProperty->name] = true;
        }

        return $attributes;
    }
}
