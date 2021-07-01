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

use Apigee\Edge\Api\Monetization\Entity\CompanyInterface;
use Apigee\Edge\Api\Monetization\Entity\DeveloperInterface;
use Apigee\Edge\Api\Monetization\Entity\LegalEntityInterface;
use Apigee\Edge\Api\Monetization\NameConverter\LegalEntityNameConvert;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class LegalEntityNormalizer extends EntityNormalizer
{
    /**
     * LegalEntityNormalizer constructor.
     *
     * @param \Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface|null $classMetadataFactory
     * @param \Symfony\Component\Serializer\NameConverter\NameConverterInterface|null $nameConverter
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface|null $propertyAccessor
     * @param \Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface|null $propertyTypeExtractor
     */
    public function __construct(?ClassMetadataFactoryInterface $classMetadataFactory = null, ?NameConverterInterface $nameConverter = null, ?PropertyAccessorInterface $propertyAccessor = null, ?PropertyTypeExtractorInterface $propertyTypeExtractor = null)
    {
        $nameConverter = $nameConverter ?? new LegalEntityNameConvert();
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor);
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress InvalidReturnType Returning an object here is required
     * for creating a valid Apigee Edge request.
     */
    public function normalize($object, $format = null, array $context = [])
    {
        /** @var object $normalized */
        $normalized = parent::normalize($object, $format, $context);

        if ($object instanceof DeveloperInterface) {
            $normalized->isCompany = false;
        } elseif ($object instanceof CompanyInterface) {
            $normalized->isCompany = true;
        }

        return $normalized;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof LegalEntityInterface;
    }
}
