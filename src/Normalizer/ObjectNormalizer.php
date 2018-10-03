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

namespace Apigee\Edge\Normalizer;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer as BaseObjectNormalizer;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Object normalizer decorator that can normalize an API response from Apigee
 * Edge to an object.
 */
class ObjectNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    /** @var \Symfony\Component\Serializer\Normalizer\ObjectNormalizer */
    private $objectNormalizer;

    /** @var \Symfony\Component\Serializer\SerializerInterface|null */
    private $serializer;

    /**
     * The API client only communicates in JSON with Apigee Edge.
     *
     * @var string
     */
    private $format = JsonEncoder::FORMAT;

    /**
     * EntityNormalizer constructor.
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
        $this->objectNormalizer = new BaseObjectNormalizer($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor);
    }

    /**
     * @inheritdoc
     *
     * @psalm-suppress InvalidReturnType stdClass is also an object.
     * @psalm-suppress PossiblyInvalidArgument First argument of array_filter is always an array.
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $asArray = $this->objectNormalizer->normalize($object, $this->format, $context);
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
        // Enforce the only supported format if format is null.
        $format = $format ?? $this->format;

        return $format === $this->format && $this->objectNormalizer->supportsNormalization($data, $format);
    }

    /**
     * @inheritdoc
     */
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
        $this->objectNormalizer->setSerializer($serializer);
    }
}
