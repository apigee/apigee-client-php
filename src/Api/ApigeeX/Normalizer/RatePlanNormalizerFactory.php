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

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;
use Symfony\Component\Serializer\SerializerInterface;
use Apigee\Edge\Api\Monetization\Normalizer\DeveloperRatePlanNormalizer;
use Apigee\Edge\Api\Monetization\Normalizer\CompanyRatePlanNormalizer;
use Apigee\Edge\Api\Monetization\Normalizer\DeveloperCategoryRatePlanNormalizer;

class RatePlanNormalizerFactory implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait {
        setSerializer as private traitSetSerializer;
    }

    /** @var \Symfony\Component\Serializer\Normalizer\NormalizerInterface[] */
    protected $normalizers = [];

    /**
     * RatePlanDenormalizerFactory constructor.
     *
     * @param \Symfony\Component\Serializer\Normalizer\NormalizerInterface[] $normalizers
     */
    public function __construct(array $normalizers = [])
    {
        $this->normalizers = array_merge($normalizers, [
            new StandardRatePlanNormalizer(),
            new DeveloperRatePlanNormalizer(),
            new CompanyRatePlanNormalizer(),
            new DeveloperCategoryRatePlanNormalizer(),
        ]);
    }

    /**
     * @inheritDoc
     *
     * @psalm-suppress InvalidNullableReturnType - There are going to be at
     * least one normalizer always that can normalize data here.
     */
    public function normalize($object, $format = null, array $context = [])
    {
        foreach ($this->normalizers as $normalizer) {
            // Return the result from the first denormalizer that can
            // denormalize this.
            if ($normalizer->supportsNormalization($object, $format)) {
                return $normalizer->normalize($object, $format, $context);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, $format = null)
    {
        foreach ($this->normalizers as $denormalizer) {
            if ($denormalizer->supportsNormalization($data, $format)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->traitSetSerializer($serializer);
        foreach ($this->normalizers as $denormalizer) {
            if ($denormalizer instanceof SerializerAwareInterface) {
                $denormalizer->setSerializer($serializer);
            }
        }
    }
}
