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

namespace Apigee\Edge\Api\Monetization\Normalizer;

use Apigee\Edge\Api\Monetization\NameConverter\ReportCriteriaNameConverter;
use Apigee\Edge\Api\Monetization\Structure\Reports\Criteria\AbstractCriteria;
use Apigee\Edge\Api\Monetization\Utility\TimezoneFixerHelperTrait;
use Apigee\Edge\Normalizer\ObjectNormalizer;
use DateTimeZone;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class ReportCriteriaNormalizer extends ObjectNormalizer
{
    use TimezoneFixerHelperTrait;

    /**
     * @var string
     */
    protected $organization;

    /**
     * ReportsCriteriaNormalizer constructor.
     *
     * @param string $organization
     * @param ClassMetadataFactoryInterface|null $classMetadataFactory
     * @param NameConverterInterface|null $nameConverter
     * @param PropertyAccessorInterface|null $propertyAccessor
     * @param PropertyTypeExtractorInterface|null $propertyTypeExtractor
     */
    public function __construct(string $organization, ?ClassMetadataFactoryInterface $classMetadataFactory = null, ?NameConverterInterface $nameConverter = null, ?PropertyAccessorInterface $propertyAccessor = null, ?PropertyTypeExtractorInterface $propertyTypeExtractor = null)
    {
        $this->organization = $organization;
        $nameConverter = $nameConverter ?? new ReportCriteriaNameConverter();
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor);
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress InvalidReturnType Returning an object here is required
     * for creating a valid Apigee Edge request.
     */
    public function normalize($object, $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        /** @var object $normalized */
        $normalized = parent::normalize($object, $format, $context);

        $addOrganizationIdCallback = function (string $id) {
            return (object) ['id' => $id, 'orgId' => $this->organization];
        };

        // Fix the structure of these properties.
        foreach (['appCriteria', 'devCriteria', 'currCriteria'] as $property) {
            $normalized->{$property} = array_map($addOrganizationIdCallback, $normalized->{$property});
        }

        // According to the API documentation it is always UTC.
        // https://docs.apigee.com/api-platform/monetization/create-reports#createreportdefapi
        $this->fixTimeZoneOnNormalization($object, $normalized, new DateTimeZone('UTC'));
        $arr_empty = [];
        // Just in case, do not send empty array values either to this API.
        foreach ($normalized as $property => $value) {
            if (is_array($value) && empty($value)) {
                // Get all the array which is empty
                $arr_empty[] = $property;
            }
        }

        foreach ($arr_empty as $val) {
            unset($normalized->{$val});
        }

        return $normalized;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof AbstractCriteria;
    }
}
