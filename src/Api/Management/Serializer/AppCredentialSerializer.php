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

use Apigee\Edge\Api\Management\Normalizer\AppCredentialNormalizer;
use Apigee\Edge\Denormalizer\CredentialProductDenormalizer;
use Apigee\Edge\Normalizer\CredentialProductNormalizer;

class AppCredentialSerializer extends AttributesPropertyAwareEntitySerializer
{
    /**
     * @inheritDoc
     */
    public function __construct(array $normalizers = [], array $encoders = [], ?\Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface $classMetadataFactory = null, ?\Symfony\Component\Serializer\NameConverter\NameConverterInterface $nameConverter = null, ?\Symfony\Component\PropertyAccess\PropertyAccessorInterface $propertyAccessor = null, ?\Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface $propertyTypeExtractor = null)
    {
        $normalizers = array_merge($normalizers, [
            new AppCredentialNormalizer(),
            new CredentialProductNormalizer(),
            new CredentialProductDenormalizer(),
        ]);
        parent::__construct($normalizers, $encoders, $classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor);
    }
}
