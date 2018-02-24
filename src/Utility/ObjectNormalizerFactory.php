<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Utility;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

/**
 * Builds an Symfony ObjectNormalizer that can normalizer all known types of objects.
 */
final class ObjectNormalizerFactory
{
    /** @var \Apigee\Edge\Utility\ObjectNormalizerDiscovery */
    private $objectNormalizerCollector;

    public function __construct(array $classMatcherOptions = [], PropertyTypeExtractorInterface $propertyTypeExtractor = null)
    {
        if (null === $propertyTypeExtractor) {
            $reflectionExtractor = new ReflectionExtractor();
            $phpDocExtractor = new PhpDocExtractor();

            $propertyTypeExtractor = new PropertyInfoExtractor(
                [
                    $reflectionExtractor,
                    $phpDocExtractor,
                ],
                // Type extractors
                [
                    $phpDocExtractor,
                    $reflectionExtractor,
                ]
            );
        }
        $this->objectNormalizerCollector = new ObjectNormalizerDiscovery($classMatcherOptions, $propertyTypeExtractor);
    }

    /**
     * @param $objectOrClass
     *
     * @throws \ReflectionException
     * @throws \Error
     *   If transformer class not not be instanced (ex.: abstract).
     * @throws \ArgumentCountError
     *   If transformer class's constructor has at least one required parameter.
     *
     * @return array
     */
    public function getNormalizers($objectOrClass)
    {
        $transformerClasses = $this->objectNormalizerCollector->getTransformers($objectOrClass);
        // Sort normalizers and denormalizers to ensure that child classes of a parent class that also in the list
        // are closer to the beginning of the array than its parents.
        // This is necessary because Serializer uses the first normalizer/denormalizer to transform the data.
        uasort($transformerClasses, function ($a, $b) {
            return in_array($b, class_parents($a)) ? -1 : 1;
        });

        $transformers = [];

        foreach ($transformerClasses as $transformerClass) {
            $transformerRc = new \ReflectionClass($transformerClass);
            $transformers[$transformerClass] = $transformerRc->newInstance();
        }

        return $transformers;
    }
}
