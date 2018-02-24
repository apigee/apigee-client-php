<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Utility;

use Apigee\Edge\Exception\ClassNotFoundException;

final class ClassMatcher
{
    /**
     * Stores mapping of entity classes by controllers.
     *
     * @var string[]
     */
    private static $classMappingCache = [];

    /**
     * @param string|object $fqcn
     * @param array $namespaceReplacements
     * @param array $classNameReplacements
     * @param string $classNameSuffix
     *
     * @throws \Apigee\Edge\Exception\ClassNotFoundException
     *
     * @return string
     */
    public static function getClass($fqcn, array $namespaceReplacements = [], array $classNameReplacements = [], string $classNameSuffix = ''): string
    {
        $fqcn = is_object($fqcn) ? get_class($fqcn) : $fqcn;
        // Same array elements in different order should not generate a different cache key.
        ksort($namespaceReplacements);
        ksort($classNameReplacements);
        $cacheKey = md5(serialize($namespaceReplacements) . serialize($classNameReplacements) . $classNameSuffix . $fqcn);
        // Try to find it in the static cache first.
        if (isset(static::$classMappingCache[$cacheKey])) {
            return static::$classMappingCache[$cacheKey];
        }
        $fqcn_parts = explode('\\', $fqcn);
        $className = array_pop($fqcn_parts);
        foreach ($fqcn_parts as $key => $part) {
            if (array_key_exists($part, $namespaceReplacements)) {
                $fqcn_parts[$key] = $namespaceReplacements[$part];
            }
        }
        $classNameParts = preg_split('/(?=[A-Z])/', $className);
        // First index is an empty string.
        array_shift($classNameParts);
        foreach ($classNameParts as $key => $part) {
            if (array_key_exists($part, $classNameReplacements)) {
                $classNameParts[$key] = $classNameReplacements[$part];
            }
        }
        $fqcn_parts[] = implode('', array_filter($classNameParts));
        $mappedFqcn = implode('\\', array_filter($fqcn_parts)) . $classNameSuffix;
        if (!class_exists($mappedFqcn)) {
            throw new ClassNotFoundException($mappedFqcn);
        }
        // Add it to the cache.
        static::$classMappingCache[$cacheKey] = $mappedFqcn;

        return static::$classMappingCache[$cacheKey];
    }
}
