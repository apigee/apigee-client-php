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

use Apigee\Edge\Api\Monetization\Entity\ReportDefinitionInterface;
use Apigee\Edge\Api\Monetization\NameConverter\ReportCriteriaNameConverter;
use Apigee\Edge\Api\Monetization\Structure\Reports\Criteria\AbstractCriteria;
use Apigee\Edge\Api\Monetization\Structure\Reports\Criteria\BillingReportCriteria;
use Apigee\Edge\Api\Monetization\Structure\Reports\Criteria\PrepaidBalanceReportCriteria;
use Apigee\Edge\Api\Monetization\Structure\Reports\Criteria\RevenueReportCriteria;
use Apigee\Edge\Api\Monetization\Utility\TimezoneFixerHelperTrait;
use Apigee\Edge\Denormalizer\ObjectDenormalizer;
use Apigee\Edge\Exception\InvalidArgumentException;
use DateTimeZone;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class ReportCriteriaDenormalizer extends ObjectDenormalizer
{
    use TimezoneFixerHelperTrait;
    /**
     * Type from the parent report definition that also defines the type of
     * the criteria object.
     */
    public const CONTEXT_REPORT_DEFINITION_TYPE = 'report_definition_type';

    /**
     * ReportsCriteriaDenormalizer constructor.
     *
     * @param ClassMetadataFactoryInterface|null $classMetadataFactory
     * @param NameConverterInterface|null $nameConverter
     * @param PropertyAccessorInterface|null $propertyAccessor
     * @param PropertyTypeExtractorInterface|null $propertyTypeExtractor
     */
    public function __construct(?ClassMetadataFactoryInterface $classMetadataFactory = null, ?NameConverterInterface $nameConverter = null, ?PropertyAccessorInterface $propertyAccessor = null, ?PropertyTypeExtractorInterface $propertyTypeExtractor = null)
    {
        $nameConverter = $nameConverter ?? new ReportCriteriaNameConverter();
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor);
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress PossiblyInvalidArgument We are sure of the return type of denormalize().
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        // This is what is in the type-hint on the $criteria property of the
        // ReportDefinition object.
        // Because "Serializing Interfaces and Abstract Classes" is only
        // available in symfony/serializer > 4.1 therefore we have to use a
        // workaround here.
        // https://symfony.com/doc/master/components/serializer.html#serializing-interfaces-and-abstract-classes
        if (AbstractCriteria::class === $type && isset($context[static::CONTEXT_REPORT_DEFINITION_TYPE])) {
            switch ($context[static::CONTEXT_REPORT_DEFINITION_TYPE]) {
                case ReportDefinitionInterface::TYPE_BILLING:
                    $type = BillingReportCriteria::class;
                    break;
                case ReportDefinitionInterface::TYPE_PREPAID_BALANCE:
                    $type = PrepaidBalanceReportCriteria::class;
                    break;
                case ReportDefinitionInterface::TYPE_REVENUE:
                    $type = RevenueReportCriteria::class;
                    break;

                default:
                    throw new InvalidArgumentException("Invalid report definition type: {$context[static::CONTEXT_REPORT_DEFINITION_TYPE]}.");
            }
        }

        foreach ($data as $property => $propertyValue) {
            if (is_array($propertyValue)) {
                foreach ($propertyValue as $item => $value) {
                    if (is_object($value) && property_exists($value, 'id') && property_exists($value, 'orgId')) {
                        $data->{$property}[$item] = $value->id;
                    }
                }
            }
        }

        // "monetizationPackageIds" and "pkgCriteria" contains the same
        // information just in different format.
        if (property_exists($data, 'pkgCriteria')) {
            foreach ($data->pkgCriteria as $pkgCriterion) {
                $data->monetizationPackageIds[] = $pkgCriterion['id'];
            }
            // Make sure values are unique.
            $data->monetizationPackageIds = array_unique($data->monetizationPackageIds);
        }

        // "prodCriteria" and "productIds" contains the same
        // information just in different format.
        if (property_exists($data, 'prodCriteria')) {
            foreach ($data->prodCriteria as $prodCriterion) {
                $data->productIds[] = $prodCriterion['id'];
            }
            // Make sure values are unique.
            $data->productIds = array_unique($data->productIds);
        }

        try {
            $denormalized = parent::denormalize($data, $type, $format, $context);
        } catch (NotNormalizableValueException $e) {
            $denormalized = $data;
        }

        // According to the API documentation it is always UTC.
        // https://docs.apigee.com/api-platform/monetization/create-reports#createreportdefapi
        $this->fixTimeZoneOnDenormalization($data, $denormalized, new DateTimeZone('UTC'));

        return $denormalized;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        // Do not apply this on array objects. ArrayDenormalizer takes care of them.
        if ('[]' === substr($type, -2)) {
            return false;
        }

        return is_a($type, AbstractCriteria::class, true);
    }
}
