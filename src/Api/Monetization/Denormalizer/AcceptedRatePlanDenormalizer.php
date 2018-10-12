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

use Apigee\Edge\Api\Monetization\Entity\AcceptedRatePlanInterface;
use Apigee\Edge\Denormalizer\ObjectDenormalizer;
use DateTimeImmutable;
use ReflectionObject;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

abstract class AcceptedRatePlanDenormalizer extends ObjectDenormalizer
{
    /**
     * @inheritDoc
     *
     * @psalm-suppress PossiblyNullReference - getPackage() can only return
     * null when a rate plan is created. It does not return null here.
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        /** @var \Apigee\Edge\Api\Monetization\Entity\AcceptedRatePlanInterface $denormalized */
        $denormalized = parent::denormalize($data, $class, $format, $context);
        // Fix the start- and end date of the rate plan if the organization's
        // timezone is different from the default PHP timezone.
        if (date_default_timezone_get() !== $denormalized->getRatePlan()->getPackage()->getOrganization()->getTimezone()->getName()) {
            $ro = new ReflectionObject($denormalized);
            $dateDenormalizer = new DateTimeNormalizer(AcceptedRatePlanInterface::DATE_FORMAT, $denormalized->getRatePlan()->getPackage()->getOrganization()->getTimezone());
            foreach ($data as $prop_name => $prop_value) {
                if ($ro->hasProperty($prop_name)) {
                    $property = $ro->getProperty($prop_name);
                    $property->setAccessible(true);
                    $value = $property->getValue($denormalized);
                    if ($value instanceof DateTimeImmutable) {
                        $property->setValue($denormalized, $dateDenormalizer->denormalize($prop_value, \DateTimeImmutable::class));
                    }
                }
            }
        }

        return $denormalized;
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        // Do not apply this on array objects. ArrayDenormalizer takes care of them.
        if ('[]' === substr($type, -2)) {
            return false;
        }

        return AcceptedRatePlanInterface::class === $type || $type instanceof AcceptedRatePlanInterface || in_array(AcceptedRatePlanInterface::class, class_implements($type));
    }
}
