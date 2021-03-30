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

namespace Apigee\Edge\Api\ApigeeX\Normalizer;

use Apigee\Edge\Api\ApigeeX\Entity\ApiProductInterface;
use Apigee\Edge\Api\Monetization\NameConverter\ApiPackageNameConverter;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Apigee\Edge\Api\Monetization\Normalizer\EntityNormalizer;

class ApiProductNormalizer extends EntityNormalizer
{
    /**
     * ApiProductNormalizer constructor.
     *
     * @param \Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface|null $classMetadataFactory
     * @param \Symfony\Component\Serializer\NameConverter\NameConverterInterface|null $nameConverter
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface|null $propertyAccessor
     * @param \Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface|null $propertyTypeExtractor
     */
    public function __construct(?ClassMetadataFactoryInterface $classMetadataFactory = null, ?NameConverterInterface $nameConverter = null, ?PropertyAccessorInterface $propertyAccessor = null, ?PropertyTypeExtractorInterface $propertyTypeExtractor = null)
    {
        $nameConverter = $nameConverter ?? new ApiPackageNameConverter();
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor);
    }

    /**
     * @inheritDoc
     *
     * @psalm-suppress InvalidReturnType Returning an object here is required
     * for creating a valid Apigee Edge request.
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $normalized = (array) parent::normalize($object, $format, $context);

        // Do not send redundant API product information to Apigee Edge, the
        // id of a referenced API product is enough.
        foreach ($normalized['product'] as $id => $data) {
            $normalized['product'][$id] = (object) ['id' => $data->id];
        }

        return (object) $normalized;
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ApiProductInterface;
    }
}
