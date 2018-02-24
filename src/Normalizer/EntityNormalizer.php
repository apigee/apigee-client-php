<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Normalizer;

use Apigee\Edge\Utility\ObjectNormalizerFactory;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class EntityNormalizer.
 *
 * Normalizes entity data to Apigee Edge's format.
 */
class EntityNormalizer implements NormalizerInterface
{
    private $classMatcherOptions = [
        ['Entity' => 'Normalizer', 'Structure' => 'Normalizer'],
        ['Interface' => ''],
        'Normalizer',
    ];

    /** @var \Symfony\Component\Serializer\Normalizer\ObjectNormalizer */
    private $objectNormalizer;

    /** @var \Apigee\Edge\Utility\ObjectNormalizerFactory */
    private $objectNormalizerFactory;

    // TODO Add missing arguments for objectNormalizer.
    public function __construct(ClassMetadataFactoryInterface $classMetadataFactory = null, NameConverterInterface $nameConverter = null, PropertyAccessorInterface $propertyAccessor = null, PropertyTypeExtractorInterface $propertyTypeExtractor = null)
    {
        // TODO move this to a parent class.
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
        $this->objectNormalizer = new ObjectNormalizer($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor);
        $this->objectNormalizerFactory = new ObjectNormalizerFactory($this->classMatcherOptions, $propertyTypeExtractor);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $normalizers = $this->objectNormalizerFactory->getNormalizers($object);
        array_unshift($normalizers, new EdgeDateNormalizer());
        $this->objectNormalizer->setSerializer(new Serializer($normalizers));

        $asArray = $this->objectNormalizer->normalize($object, $format, $context);
        // Exclude null values from the output, even if PATCH is not supported on Apigee Edge
        // sending a smaller portion of data in POST/PUT is always a good practice.
        $asArray = array_filter($asArray, function ($value) {
            return !is_null($value);
        });
        ksort($asArray);

        return (object) $asArray;
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return \is_object($data) && !$data instanceof \Traversable;
    }
}
