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

namespace Apigee\Edge\Entity;

use Apigee\Edge\Denormalizer\EdgeDateDenormalizer;
use Apigee\Edge\Denormalizer\EntityDenormalizer;
use Apigee\Edge\Denormalizer\KeyValueMapDenormalizer;
use Apigee\Edge\Normalizer\EdgeDateNormalizer;
use Apigee\Edge\Normalizer\EntityNormalizer;
use Apigee\Edge\Normalizer\KeyValueMapNormalizer;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Serializes, normalizes and denormalizes entities.
 */
class EntityTransformer implements EntityTransformerInterface
{
    /** @var \Symfony\Component\Serializer\Serializer */
    private $serializer;

    /**
     * EntityTransformer constructor.
     *
     * @param \Symfony\Component\Serializer\Normalizer\NormalizerInterface[]|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface[] $normalizers
     * @param \Symfony\Component\Serializer\Encoder\EncoderInterface[]|\Symfony\Component\Serializer\Encoder\DecoderInterface[] $encoders
     * @param \Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface|null $classMetadataFactory
     * @param \Symfony\Component\Serializer\NameConverter\NameConverterInterface|null $nameConverter
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface|null $propertyAccessor
     * @param \Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface|null $propertyTypeExtractor
     */
    public function __construct(array $normalizers = [], array $encoders = [], ClassMetadataFactoryInterface $classMetadataFactory = null, NameConverterInterface $nameConverter = null, PropertyAccessorInterface $propertyAccessor = null, PropertyTypeExtractorInterface $propertyTypeExtractor = null)
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
        $normalizers = array_merge($normalizers, [
            // KVM is a commonly used object type. Let's make its normalizers/denormalizers available by default
            new KeyValueMapNormalizer(),
            new KeyValueMapDenormalizer(),
            // Transforms Unix epoch timestamps to date objects and vice-versa.
            new EdgeDateNormalizer(),
            new EdgeDateDenormalizer(),
            // Takes care of denormalizations of array objects.
            new ArrayDenormalizer(),
            new EntityNormalizer($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor),
            new EntityDenormalizer($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor),
            ]
        );
        // Keep the same structure that we get from Apigee Edge, do not transforms objects to arrays.
        $encoders = [new JsonEncoder(null, new JsonDecode())];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return $this->serializer->denormalize($data, $class, $format, $context);
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $this->serializer->supportsDenormalization($data, $type, $format);
    }

    /**
     * @inheritdoc
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return $this->serializer->normalize($object, $format, $context);
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $this->serializer->supportsNormalization($data, $format);
    }

    /**
     * @inheritdoc
     */
    public function serialize($data, $format, array $context = [])
    {
        return $this->serializer->serialize($data, $format, $context);
    }

    /**
     * @inheritdoc
     */
    public function deserialize($data, $type, $format, array $context = [])
    {
        return $this->serializer->deserialize($data, $type, $format, $context);
    }

    /**
     * @inheritdoc
     */
    public function setPropertiesFromResponse(ResponseInterface $response, EntityInterface $entity): void
    {
        // Parse Edge response to a temporary entity (with the same type as $entity).
        // This is a crucial step because Edge response must be transformed before we would be able use it with some
        // of our setters (ex.: attributes).
        $tmp = $this->deserialize(
            (string) $response->getBody(),
            get_class($entity),
            'json'
        );
        $ro = new \ReflectionObject($entity);
        // Copy property values from the temporary entity to $entity.
        foreach ($ro->getProperties() as $property) {
            // Ensure that these methods are exist. This is always true for all SDK entities but we can not be sure
            // about custom implementation.
            $setter = 'set' . ucfirst($property->getName());
            if (!$ro->hasMethod($setter)) {
                continue;
            }

            $getter = 'get' . ucfirst($property->getName());
            if (!$ro->hasMethod($getter)) {
                $getter = 'is' . ucfirst($property->getName());
                if (!$ro->hasMethod($getter)) {
                    continue;
                }
            }

            $rm = new \ReflectionMethod($entity, $setter);
            $value = $tmp->{$getter}();
            // Exclude null values.
            // (An entity property value is null (internally) if it is scalar and the Edge response from
            // the entity object has been created did not contain value for the property.)
            if (null !== $value) {
                $rm->invoke($entity, $value);
            }
        }
    }
}
