<?php

/*
 * Copyright 2021 Google LLC
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

namespace Apigee\Edge\Api\ApigeeX\Denormalizer;

use Apigee\Edge\Api\ApigeeX\Entity\RatePlanInterface;
use Apigee\Edge\Api\ApigeeX\NameConverter\RatePlanNameConverter;
use Apigee\Edge\Api\Monetization\Utility\TimezoneFixerHelperTrait;
use Apigee\Edge\Denormalizer\ObjectDenormalizer;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * Base class for denormalizing rate plan entities.
 */
abstract class RatePlanDenormalizer extends ObjectDenormalizer
{
    use TimezoneFixerHelperTrait;

    /**
     * RatePlanDenormalizer constructor.
     *
     * @param \Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface|null $classMetadataFactory
     * @param \Symfony\Component\Serializer\NameConverter\NameConverterInterface|null $nameConverter
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface|null $propertyAccessor
     * @param \Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface|null $propertyTypeExtractor
     */
    public function __construct(?ClassMetadataFactoryInterface $classMetadataFactory = null, ?NameConverterInterface $nameConverter = null, ?PropertyAccessorInterface $propertyAccessor = null, ?PropertyTypeExtractorInterface $propertyTypeExtractor = null)
    {
        $nameConverter = $nameConverter ?? new RatePlanNameConverter();
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor);
    }

    /**
     * @inheritdoc
     *
     * @psalm-suppress PossiblyNullReference - Organization should not be null
     * here.
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        /** @var \Apigee\Edge\Api\ApigeeX\Entity\RatePlanInterface $entity */
        $entity = parent::denormalize($data, $class, $format, $context);
        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        // Do not apply this on array objects. ArrayDenormalizer takes care of them.
        if ('[]' === substr($type, -2)) {
            return false;
        }

        return RatePlanInterface::class === $type || $type instanceof RatePlanInterface || in_array(RatePlanInterface::class, class_implements($type));
    }
}
