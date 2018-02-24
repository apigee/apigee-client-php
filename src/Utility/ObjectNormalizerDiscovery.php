<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Utility;

use Apigee\Edge\Exception\ClassNotFoundException;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

final class ObjectNormalizerDiscovery
{
    /** @var \Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface */
    private $propertyTypeExtractor;

    private $classMatcherOptions = [];

    /** @var array */
    private static $processedObjectPropertyTypes = [];

    /** @var \Apigee\Edge\Utility\ObjectPropertyExtractor */
    private $objectPropertyExtractor;

    public function __construct(array $classMatcherOptions = [], PropertyTypeExtractorInterface $propertyTypeExtractor)
    {
        $this->propertyTypeExtractor = $propertyTypeExtractor;
        $this->classMatcherOptions = $classMatcherOptions;
        $this->objectPropertyExtractor = new ObjectPropertyExtractor();
    }

    /**
     * @param $objectOrClass
     *
     * @throws \ReflectionException
     *
     * @return array
     */
    public function getTransformers($objectOrClass): array
    {
        $class = is_object($objectOrClass) ? get_class($objectOrClass) : rtrim($objectOrClass, '[]');
        $transformers = [];
        // $transformers = $this->getObjectPropertyTransformers($class);
        foreach ($this->objectPropertyExtractor->extractAttributes($class) as $property) {
            if (null === $types = $this->propertyTypeExtractor->getTypes($class, $property)) {
                continue;
            }
            /** @var \Symfony\Component\PropertyInfo\Type[] $types */
            foreach ($types as $type) {
                list('builtInType' => $builtInType, 'class' => $propertyClass) = $this->getPropertyTypeInfo($type);
                if (Type::BUILTIN_TYPE_OBJECT === $builtInType) {
                    // Get all transformer(s) that can be used to transform this property.
                    // In case of AttributesProperty it can ne the AttributesPropertyDenormalizer and KeyValueMapNormalizer.
                    $transformers += $this->getObjectPropertyTransformers($propertyClass);
                    // Get transformers that required to transform all object properties of this class.
                    $transformers += $this->getTransformers($propertyClass);
                }
            }
        }

        return $transformers;
    }

    /**
     * @param $class
     *
     * @throws \ReflectionException
     *
     * @return array
     */
    private function getObjectPropertyTransformers($class): array
    {
        list($namespaceReplacements, $classNameReplacements, $classNameSuffix) = $this->classMatcherOptions;
        $transformers = [];
        $propertyRc = new \ReflectionClass($class);
        do {
            // Do not try to find normalizers/denormalizers for internal classes.
            if (!$propertyRc->isUserDefined()) {
                continue;
            }
            $cacheKey = $classNameSuffix . '.' . $propertyRc->getName();
            if (array_key_exists($cacheKey, self::$processedObjectPropertyTypes)) {
                $transformerClass = self::$processedObjectPropertyTypes[$cacheKey];
            } else {
                try {
                    $transformerClass = ClassMatcher::getClass($propertyRc->getName(), $namespaceReplacements, $classNameReplacements, $classNameSuffix);
                    self::$processedObjectPropertyTypes[$cacheKey] = $transformerClass;
                    $transformers[$transformerClass] = $transformerClass;
                } catch (ClassNotFoundException $e) {
                    continue;
                }
            }
            $transformers[$transformerClass] = $transformerClass;
        } while ($propertyRc = $propertyRc->getParentClass());
        // TODO This is unable to find normalizers by a type-hint that refers to an interface.
        // Ex.: DeveloperApp::$credentials.

        return $transformers;
    }

    /**
     * @param \Symfony\Component\PropertyInfo\Type $type
     *
     * @return array
     */
    private function getPropertyTypeInfo(Type $type): array
    {
        $builtinType = $type->getBuiltinType();
        $class = $type->getClassName();
        $collectionKeyType = null;
        if ($type->isCollection() && null !== ($collectionValueType = $type->getCollectionValueType()) && Type::BUILTIN_TYPE_OBJECT === $collectionValueType->getBuiltinType()) {
            $builtinType = Type::BUILTIN_TYPE_OBJECT;
            $class = $collectionValueType->getClassName();
            $collectionKeyType = $type->getCollectionKeyType();
        }

        return [
            'builtInType' => $builtinType,
            'class' => $class,
            'collectionKeyType' => $collectionKeyType,
        ];
    }
}
