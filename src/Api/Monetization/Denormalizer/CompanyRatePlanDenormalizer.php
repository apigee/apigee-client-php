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

use Apigee\Edge\Api\Monetization\Entity\CompanyRatePlan;
use Apigee\Edge\Api\Monetization\Entity\CompanyRatePlanRevision;
use Apigee\Edge\Api\Monetization\Entity\RatePlanInterface;
use Apigee\Edge\Api\Monetization\NameConverter\CompanyRatePlanNameConverter;

class CompanyRatePlanDenormalizer extends RatePlanDenormalizer
{
    /**
     * Fully qualified class name of the company rate plan entity.
     *
     * @var string
     */
    protected $companyRatePlanClass = CompanyRatePlan::class;

    /**
     * Fully qualified class name of the company rate plan revision entity.
     *
     * @var string
     */
    protected $companyRatePlanRevisionClass = CompanyRatePlanRevision::class;

    /**
     * CompanyRatePlanDenormalizer constructor.
     *
     * @param null|\Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface $classMetadataFactory
     * @param null|\Symfony\Component\Serializer\NameConverter\NameConverterInterface $nameConverter
     * @param null|\Symfony\Component\PropertyAccess\PropertyAccessorInterface $propertyAccessor
     * @param null|\Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface $propertyTypeExtractor
     */
    public function __construct(?\Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface $classMetadataFactory = null, ?\Symfony\Component\Serializer\NameConverter\NameConverterInterface $nameConverter = null, ?\Symfony\Component\PropertyAccess\PropertyAccessorInterface $propertyAccessor = null, ?\Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface $propertyTypeExtractor = null)
    {
        $nameConverter = $nameConverter ?? new CompanyRatePlanNameConverter();
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor);
    }

    /**
     * @inheritDoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data->parentRatePlan)) {
            return parent::denormalize($data, $this->companyRatePlanRevisionClass, $format, $context);
        }

        return parent::denormalize($data, $this->companyRatePlanClass, $format, $context);
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

        if (parent::supportsDenormalization($data, $type, $format)) {
            return RatePlanInterface::TYPE_DEVELOPER == $data->type && $data->developer->isCompany;
        }

        return false;
    }
}
