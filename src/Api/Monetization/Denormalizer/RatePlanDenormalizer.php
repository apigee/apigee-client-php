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

namespace Apigee\Edge\Api\Monetization\Denormalizer;

use Apigee\Edge\Api\Monetization\Entity\RatePlanInterface;
use Apigee\Edge\Api\Monetization\NameConverter\RatePlanNameConverter;
use Apigee\Edge\Denormalizer\ObjectDenormalizer;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

/**
 * Base class for denormalizing rate plan entities.
 */
abstract class RatePlanDenormalizer extends ObjectDenormalizer
{
    /**
     * RatePlanDenormalizer constructor.
     *
     * @param null|\Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface $classMetadataFactory
     * @param null|\Symfony\Component\Serializer\NameConverter\NameConverterInterface $nameConverter
     * @param null|\Symfony\Component\PropertyAccess\PropertyAccessorInterface $propertyAccessor
     * @param null|\Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface $propertyTypeExtractor
     */
    public function __construct(?ClassMetadataFactoryInterface $classMetadataFactory = null, ?NameConverterInterface $nameConverter = null, ?PropertyAccessorInterface $propertyAccessor = null, ?PropertyTypeExtractorInterface $propertyTypeExtractor = null)
    {
        $nameConverter = $nameConverter ?? new RatePlanNameConverter();
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor);
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        // To be able to transform date strings to proper date objects we need
        // the timezone, but it is only available on the organization profile
        // object. Luckily all rate plans have a reference to the parent
        // organization profile.
        $startDate = $data->startDate;
        $endDate = property_exists($data, 'endDate') ? $data->endDate : null;

        $entity = parent::denormalize($data, $class, $format, $context);

        // Fix the start- and end date of the rate plan if the organization's
        // timezone is different from the default PHP timezone.
        if (date_default_timezone_get() !== $entity->getOrganization()->getTimezone()->getName()) {
            $dateDenormalizer = new DateTimeNormalizer(RatePlanInterface::DATE_FORMAT, $entity->getOrganization()->getTimezone());
            $entity->setStartDate($dateDenormalizer->denormalize($startDate, \DateTimeImmutable::class));
            if (null !== $endDate) {
                $entity->setEndDate($dateDenormalizer->denormalize($endDate, \DateTimeImmutable::class));
            }
        }

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
