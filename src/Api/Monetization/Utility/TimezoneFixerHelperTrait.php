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

namespace Apigee\Edge\Api\Monetization\Utility;

use Apigee\Edge\Api\Monetization\Entity\EntityInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

/**
 * Contains utility functions for fixing timezone of date objects.
 */
trait TimezoneFixerHelperTrait
{
    /**
     * Fixes the timezone of all DateTimeImmutable objects before the entity
     * is being sent to Apigee Edge.
     *
     * It process object properties non-recursively because it gets called
     * on nested object automatically.
     *
     * @param object $object
     *   The object that has been normalized.
     * @param $normalized
     *   The normalized object.
     * @param \DateTimeZone $orgTimezone
     *   Timezone of the organization retrieved from the organization profile.
     */
    protected function fixTimeZoneOnNormalization($object, $normalized, \DateTimeZone $orgTimezone): void
    {
        // Change timezone of all normalized dates to organization's current
        // timezone if it is different than the default PHP timezone.
        if (date_default_timezone_get() !== $orgTimezone->getName()) {
            $ro = new \ReflectionObject($object);
            $dateDenormalizer = new DateTimeNormalizer(EntityInterface::DATE_FORMAT, $orgTimezone);
            foreach ($ro->getProperties() as $property) {
                $property->setAccessible(true);
                $value = $property->getValue($object);
                if ($value instanceof \DateTimeImmutable) {
                    $normalized->{$property->getName()} = $dateDenormalizer->normalize($value, \DateTimeImmutable::class);
                }
            }
        }
    }

    /**
     * Corrects the timezone on all DateTimeImmutable objects if the
     * organization's current timezone is not the default UTC.
     *
     * It process object properties non-recursively because it gets called
     * on nested object automatically.
     *
     * @param object $object
     *   The object that got denormalized.
     * @param object $denormalized
     *   The denormalized object.
     * @param \DateTimeZone $orgTimezone
     *   Timezone of the organization retrieved from the organization profile.
     */
    protected function fixTimeZoneOnDenormalization($object, $denormalized, \DateTimeZone $orgTimezone): void
    {
        // Change timezone of all date objects to organization's current
        // timezone if it is different than the default PHP timezone.

        if (date_default_timezone_get() !== $orgTimezone->getName()) {
            $ro = new \ReflectionObject($denormalized);
            $dateDenormalizer = new DateTimeNormalizer(EntityInterface::DATE_FORMAT, $orgTimezone);
            foreach ($object as $prop_name => $prop_value) {
                if ($ro->hasProperty($prop_name)) {
                    $property = $ro->getProperty($prop_name);
                    $property->setAccessible(true);
                    $value = $property->getValue($denormalized);
                    if ($value instanceof \DateTimeImmutable) {
                        $property->setValue($denormalized, $dateDenormalizer->denormalize($prop_value, \DateTimeImmutable::class));
                    }
                }
            }
        }
    }
}
