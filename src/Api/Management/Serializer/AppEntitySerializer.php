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

namespace Apigee\Edge\Api\Management\Serializer;

use Apigee\Edge\Api\Management\Denormalizer\AppDenormalizer;
use Apigee\Edge\Api\Management\Normalizer\AppCredentialNormalizer;
use Apigee\Edge\Api\Management\Normalizer\AppNormalizer;
use Apigee\Edge\Denormalizer\AttributesPropertyDenormalizer;
use Apigee\Edge\Denormalizer\CredentialProductDenormalizer;
use Apigee\Edge\Normalizer\CredentialProductNormalizer;
use Apigee\Edge\Serializer\EntitySerializer;

class AppEntitySerializer extends EntitySerializer
{
    /**
     * AppEntitySerializer constructor.
     *
     * @param array $normalizers
     */
    public function __construct($normalizers = [])
    {
        $normalizers = array_merge($normalizers, [
            new CredentialProductDenormalizer(),
            new CredentialProductNormalizer(),
            new AttributesPropertyDenormalizer(),
            new AppCredentialNormalizer(),
            new AppNormalizer(),
            new AppDenormalizer(),
        ]);
        parent::__construct($normalizers);
    }
}
