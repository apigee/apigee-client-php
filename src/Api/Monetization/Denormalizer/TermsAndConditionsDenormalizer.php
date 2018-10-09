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

use Apigee\Edge\Api\Monetization\Entity\TermsAndConditionsInterface;
use Apigee\Edge\Api\Monetization\NameConverter\TermsAndConditionsNameConverter;
use Apigee\Edge\Denormalizer\ObjectDenormalizer;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class TermsAndConditionsDenormalizer extends ObjectDenormalizer
{
    /**
     * TermsAndConditionsDenormalizer constructor.
     *
     * @param null|\Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface $classMetadataFactory
     * @param null|\Symfony\Component\Serializer\NameConverter\NameConverterInterface $nameConverter
     * @param null|\Symfony\Component\PropertyAccess\PropertyAccessorInterface $propertyAccessor
     * @param null|\Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface $propertyTypeExtractor
     */
    public function __construct(?ClassMetadataFactoryInterface $classMetadataFactory = null, ?NameConverterInterface $nameConverter = null, ?PropertyAccessorInterface $propertyAccessor = null, ?PropertyTypeExtractorInterface $propertyTypeExtractor = null)
    {
        $nameConverter = $nameConverter ?? new TermsAndConditionsNameConverter();
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor);
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        // To be able to transform date strings to proper date objects we need
        // the timezone, but it is only available on the organization profile
        // object. Luckily all tncs have a reference to the parent
        // organization profile.
        $startDate = $data->startDate;

        $entity = parent::denormalize($data, $class, $format, $context);

        // Fix the start date of the tnc if the organization's
        // timezone is different from the default PHP timezone.
        if (date_default_timezone_get() !== $entity->getOrganization()->getTimezone()->getName()) {
            $dateDenormalizer = new DateTimeNormalizer(TermsAndConditionsInterface::DATE_FORMAT, $entity->getOrganization()->getTimezone());
            $entity->setStartDate($dateDenormalizer->denormalize($startDate, \DateTimeImmutable::class));
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

        return TermsAndConditionsInterface::class === $type || $type instanceof TermsAndConditionsInterface || in_array(TermsAndConditionsInterface::class, class_implements($type));
    }
}
