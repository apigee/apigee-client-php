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

namespace Apigee\Edge\Denormalizer;

use Apigee\Edge\PropertyAccess\PropertyAccessorDecorator;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer as BaseObjectNormalizer;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Object normalizer decorator that can denormalize an object to a valid
 * payload for Apigee Edge.
 */
class ObjectDenormalizer implements DenormalizerInterface, SerializerAwareInterface
{
    /** @var \Symfony\Component\Serializer\Normalizer\ObjectNormalizer */
    private $objectNormalizer;

    /** @var \Symfony\Component\Serializer\SerializerInterface */
    private $serializer;

    /**
     * The API client only communicates in JSON with Apigee Edge.
     *
     * @var string
     */
    private $format = JsonEncoder::FORMAT;

    /**
     * EntityDenormalizer constructor.
     *
     * @param \Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface|null $classMetadataFactory
     * @param \Symfony\Component\Serializer\NameConverter\NameConverterInterface|null $nameConverter
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface|null $propertyAccessor
     * @param \Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface|null $propertyTypeExtractor
     */
    public function __construct(ClassMetadataFactoryInterface $classMetadataFactory = null, NameConverterInterface $nameConverter = null, PropertyAccessorInterface $propertyAccessor = null, PropertyTypeExtractorInterface $propertyTypeExtractor = null)
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

        if (null === $propertyAccessor) {
            // BaseObjectNormalizer would do the same.
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        $this->objectNormalizer = new BaseObjectNormalizer($classMetadataFactory, $nameConverter, new PropertyAccessorDecorator($propertyAccessor), $propertyTypeExtractor);
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        // The original input should not be altered.
        if (is_object($data)) {
            $cleanData = clone $data;
        } else {
            $cleanData = $data;
        }
        // Exclude empty arrays from the denormalization process since
        // we are using variable-length arguments in entity setters instead
        // of arrays.
        // @see \Apigee\Edge\PropertyAccess\PropertyAccessorDecorator::setValue()
        foreach ($cleanData as $key => $value) {
            if (is_array($value) && empty($value)) {
                unset($cleanData->{$key});
            }
        }

        return $this->objectNormalizer->denormalize($cleanData, $type, $this->format, $context);
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ('[]' === substr($type, -2)) {
            return false;
        }

        // Enforce the only supported format if format is null.
        $format = $format ?? $this->format;

        return $format === $this->format && $this->objectNormalizer->supportsDenormalization($data, $type, $format);
    }

    /**
     * @inheritdoc
     */
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
        $this->objectNormalizer->setSerializer($this->serializer);
    }
}
