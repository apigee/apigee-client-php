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

namespace Apigee\Edge\Api\ApigeeX\Serializer;

use Apigee\Edge\Api\ApigeeX\Denormalizer\ApiProductDenormalizer;
use Apigee\Edge\Api\ApigeeX\Normalizer\ApiProductNormalizer;
use Apigee\Edge\Api\Monetization\Serializer\EntitySerializer;

class ApiProductSerializer extends EntitySerializer
{
    /**
     * @inheritDoc
     */
    public static function getEntityTypeSpecificDefaultNormalizers(): array
    {
        $normalizers = parent::getEntityTypeSpecificDefaultNormalizers();

        return array_merge(
            [
                new ApiProductDenormalizer(),
                new ApiProductNormalizer(),
            ],
            $normalizers
        );
    }
}
