<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Denormalizer;

use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Utility\ObjectNormalizerFactory;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class EntityDenormalizer.
 *
 * Denormalizes an entity from Apigee Edge's response to our internal structure.
 */
class EntityDenormalizer implements DenormalizerInterface
{
    private $classMatcherOptions = [
        ['Entity' => 'Denormalizer', 'Structure' => 'Denormalizer'],
        ['Interface' => ''],
        'Denormalizer',
    ];

    /** @var \Symfony\Component\Serializer\Normalizer\ObjectNormalizer */
    private $objectNormalizer;

    /** @var \Apigee\Edge\Utility\ObjectNormalizerFactory */
    private $objectNormalizerFactory;

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

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $denormalizers = $this->objectNormalizerFactory->getNormalizers($class);
        array_unshift($denormalizers, new EdgeDateDenormalizer());
        $this->objectNormalizer->setSerializer(new Serializer($denormalizers));

        if (is_array($data)) {
            $denormalized = [];
            $class = rtrim($class, '[]');
            foreach ($data as $item) {
                $denormalized[] = $this->objectNormalizer->denormalize($item, $class, $format, $context);
            }

            return $denormalized;
        }

        return $this->objectNormalizer->denormalize($data, $class, $format, $context);
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        $type = rtrim($type, '[]');

        return $type instanceof EntityInterface || in_array(EntityInterface::class, class_implements($type));
    }
}
