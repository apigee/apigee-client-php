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

namespace Apigee\Edge\Api\Monetization\Denormalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;
use Symfony\Component\Serializer\SerializerInterface;

class RatePlanDenormalizerFactory implements DenormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait {
        setSerializer as private traitSetSerializer;
    }

    /** @var \Symfony\Component\Serializer\Normalizer\DenormalizerInterface[] */
    protected $denormalizers = [];

    /**
     * RatePlanDenormalizerFactory constructor.
     *
     * @param \Symfony\Component\Serializer\Normalizer\DenormalizerInterface[] $denormalizers
     */
    public function __construct(array $denormalizers = [])
    {
        $this->denormalizers = array_merge($denormalizers, [
            new StandardRatePlanDenormalizer(),
            new DeveloperRatePlanDenormalizer(),
            new CompanyRatePlanDenormalizer(),
            new DeveloperCategoryRatePlanDenormalizer(),
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress InvalidNullableReturnType - There are going to be at
     * least one denormalizer always that can denormalize data here.
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        foreach ($this->denormalizers as $denormalizer) {
            // Return the result from the first denormalizer that can
            // denormalize this.
            if ($denormalizer->supportsDenormalization($data, $type, $format)) {
                return $denormalizer->denormalize($data, $type, $format, $context);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        foreach ($this->denormalizers as $denormalizer) {
            if ($denormalizer->supportsDenormalization($data, $type, $format)) {
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
        foreach ($this->denormalizers as $denormalizer) {
            if ($denormalizer instanceof SerializerAwareInterface) {
                $denormalizer->setSerializer($serializer);
            }
        }
    }
}
