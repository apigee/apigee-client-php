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

abstract class LegalEntityReportDefinitionNormalizer extends ReportDefinitionNormalizer
{
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
        // In case of POST requests there is no problem if the developer
        // (or company) reference is in the API request, but in case of PUT
        // request it causes a mint.databaseException.
        // @see \Apigee\Edge\Api\Monetization\Controller\LegalEntityReportDefinitionController
        unset($normalized->developer);

        return $normalized;
    }
}
