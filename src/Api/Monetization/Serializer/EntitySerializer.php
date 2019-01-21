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

use Apigee\Edge\Api\Monetization\Denormalizer\DateTimeZoneDenormalizer;
use Apigee\Edge\Api\Monetization\Entity\EntityInterface;
use Apigee\Edge\Api\Monetization\Normalizer\DateTimeZoneNormalizer;
use Apigee\Edge\Api\Monetization\Normalizer\EntityNormalizer;
use Apigee\Edge\Serializer\EntitySerializer as BaseEntitySerializer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class EntitySerializer extends BaseEntitySerializer
{
    /**
     * EntitySerializer constructor.
     *
     * @param array $normalizers
     *
     * @psalm-suppress InvalidArgument
     * Required since symfony/serializer >= 4.2.0
     * @see https://github.com/symfony/symfony/pull/28709
     */
    public function __construct($normalizers = [])
    {
        $normalizers = array_merge(
            $normalizers,
            static::getEntityTypeSpecificDefaultNormalizers(),
            [
                // Apigee Edge's default timezone is UTC, let's pass it as
                // timezone instead of user's current timezone.
                new DateTimeNormalizer(EntityInterface::DATE_FORMAT, new \DateTimeZone('UTC')),
                new DateTimeZoneDenormalizer(),
                new DateTimeZoneNormalizer(),
                new EntityNormalizer(),
            ]
        );
        parent::__construct($normalizers);
    }

    /**
     * Returns the default entity type specific normalizers used by the serializer.
     *
     * In case of Monetization API, thanks for the nested entity references,
     * we have to expose normalizers used by an entity specific serializer to
     * be able to easily normalized nested entity references in an entity
     * specific normalizer (without code duplication).
     *
     * @return \Symfony\Component\Serializer\Normalizer\NormalizerInterface[]|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface[]
     */
    public static function getEntityTypeSpecificDefaultNormalizers(): array
    {
        return [];
    }
}
