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

use Apigee\Edge\Api\Monetization\Denormalizer\RatePlanDenormalizerFactory;
use Apigee\Edge\Api\Monetization\Denormalizer\RatePlanRateDenormalizer;
use Apigee\Edge\Api\Monetization\Normalizer\RatePlanNormalizerFactory;
use Apigee\Edge\Api\Monetization\Normalizer\RatePlanRateNormalizer;

class RatePlanSerializer extends EntitySerializer
{
    /**
     * @inheritDoc
     */
    public static function getEntityTypeSpecificDefaultNormalizers(): array
    {
        $normalizers = parent::getEntityTypeSpecificDefaultNormalizers();

        return array_merge(
            [
                new RatePlanRateDenormalizer(),
                new RatePlanRateNormalizer(),
                new RatePlanDenormalizerFactory(),
                new RatePlanNormalizerFactory(),
            ],
            ApiPackageSerializer::getEntityTypeSpecificDefaultNormalizers(),
            SupportedCurrencySerializer::getEntityTypeSpecificDefaultNormalizers(),
            // Because of the developer/company specific rate plans we need
            // this.
            LegalEntitySerializer::getEntityTypeSpecificDefaultNormalizers(),
            $normalizers
        );
    }
}
