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

use Apigee\Edge\Api\Monetization\Denormalizer\CompanyReportDefinitionDenormalizer;
use Apigee\Edge\Api\Monetization\Denormalizer\DeveloperReportDefinitionDenormalizer;
use Apigee\Edge\Api\Monetization\Denormalizer\ReportCriteriaDenormalizer;
use Apigee\Edge\Api\Monetization\Denormalizer\ReportDefinitionDenormalizer;
use Apigee\Edge\Api\Monetization\Normalizer\CompanyReportDefinitionNormalizer;
use Apigee\Edge\Api\Monetization\Normalizer\DeveloperReportDefinitionNormalizer;
use Apigee\Edge\Api\Monetization\Normalizer\ReportCriteriaNormalizer;
use Apigee\Edge\Api\Monetization\Normalizer\ReportDefinitionNormalizer;

class ReportDefinitionSerializer extends EntitySerializer
{
    /**
     * ReportDefinitionSerializer constructor.
     *
     * @param string $organization
     * @param array $normalizers
     */
    public function __construct(string $organization, array $normalizers = [])
    {
        $normalizers = array_merge([
            new ReportCriteriaDenormalizer(),
            new ReportCriteriaNormalizer($organization),
        ], $normalizers);
        parent::__construct($normalizers);
    }

    /**
     * @inheritDoc
     */
    public static function getEntityTypeSpecificDefaultNormalizers(): array
    {
        $normalizers = parent::getEntityTypeSpecificDefaultNormalizers();

        return array_merge(
            [
                new CompanyReportDefinitionDenormalizer(),
                new CompanyReportDefinitionNormalizer(),
                new DeveloperReportDefinitionDenormalizer(),
                new DeveloperReportDefinitionNormalizer(),
                new ReportDefinitionDenormalizer(),
                new ReportDefinitionNormalizer(),
            ],
            LegalEntitySerializer::getEntityTypeSpecificDefaultNormalizers(),
            OrganizationProfileSerializer::getEntityTypeSpecificDefaultNormalizers(),
            $normalizers
        );
    }

    /**
     * @inheritdoc
     */
    public function deserialize($data, $type, $format, array $context = [])
    {
        // Because of the decorator pattern in the EntitySerializer we have to
        // repeat this in here as well.
        if ($this->supportsDecoding($format)) {
            $decoded = $this->decode($data, $format, $context);
            $this->addReportDefinitionTypeToContext($decoded, $context);
        }

        return parent::deserialize($data, $type, $format, $context);
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $this->addReportDefinitionTypeToContext($data, $context);

        return parent::denormalize($data, $type, $format, $context);
    }

    /**
     * Add report definition type to the serialization context.
     *
     * @param mixed $data
     * @param array $context
     */
    final protected function addReportDefinitionTypeToContext($data, array &$context): void
    {
        if (is_object($data) && property_exists($data, 'type')) {
            $context[ReportCriteriaDenormalizer::CONTEXT_REPORT_DEFINITION_TYPE] = $data->type;
        }
    }
}
