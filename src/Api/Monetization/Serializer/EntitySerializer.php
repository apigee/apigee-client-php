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

namespace Apigee\Edge\Api\Monetization\Serializer;

use Apigee\Edge\Api\Monetization\Denormalizer\DateTimeZoneDenormalizer;
use Apigee\Edge\Api\Monetization\Denormalizer\EntityDenormalizer;
use Apigee\Edge\Api\Monetization\Entity\EntityInterface;
use Apigee\Edge\Api\Monetization\Normalizer\DateTimeZoneNormalizer;
use Apigee\Edge\Api\Monetization\Normalizer\EntityNormalizer;
use Apigee\Edge\Serializer\EntitySerializer as BaseEntitySerializer;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class EntitySerializer extends BaseEntitySerializer
{
    /**
     * EntitySerializer constructor.
     *
     * @param array $normalizers
     * @param array $encoders
     * @param null|ClassMetadataFactoryInterface $classMetadataFactory
     * @param null|NameConverterInterface $nameConverter
     * @param null|PropertyAccessorInterface $propertyAccessor
     * @param null|PropertyTypeExtractorInterface $propertyTypeExtractor
     */
    public function __construct($normalizers = [], $encoders = [], ?ClassMetadataFactoryInterface $classMetadataFactory = null, ?NameConverterInterface $nameConverter = null, ?PropertyAccessorInterface $propertyAccessor = null, ?PropertyTypeExtractorInterface $propertyTypeExtractor = null)
    {
        $normalizers = array_merge(
            $normalizers,
            static::getEntityTypeSpecificDefaultNormalizers($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor),
            [
                // Apigee Edge's default timezone is UTC, let's pass it as
                // timezone instead of user's current timezone.
                new DateTimeNormalizer(EntityInterface::DATE_FORMAT, new \DateTimeZone('UTC')),
                new DateTimeZoneDenormalizer(),
                new DateTimeZoneNormalizer(),
                new EntityDenormalizer($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor),
                new EntityNormalizer($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor),
            ]
        );
        parent::__construct($normalizers, $encoders, $classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor);
    }

    /**
     * Returns the default entity type specific normalizers used by the serializer.
     *
     * @param null|\Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface $classMetadataFactory
     * @param null|\Symfony\Component\Serializer\NameConverter\NameConverterInterface $nameConverter
     * @param null|\Symfony\Component\PropertyAccess\PropertyAccessorInterface $propertyAccessor
     * @param null|\Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface $propertyTypeExtractor
     *
     * @return \Symfony\Component\Serializer\Normalizer\NormalizerInterface[]|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface[]
     */
    public static function getEntityTypeSpecificDefaultNormalizers(?ClassMetadataFactoryInterface $classMetadataFactory = null, ?NameConverterInterface $nameConverter = null, ?PropertyAccessorInterface $propertyAccessor = null, ?PropertyTypeExtractorInterface $propertyTypeExtractor = null): array
    {
        return [];
    }
}
