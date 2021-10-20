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

namespace Apigee\Edge\Api\ApigeeX\Denormalizer;

use Apigee\Edge\Api\ApigeeX\Entity\AcceptedRatePlanInterface;
use Apigee\Edge\Api\Monetization\Utility\TimezoneFixerHelperTrait;
use Apigee\Edge\Denormalizer\ObjectDenormalizer;

abstract class AcceptedRatePlanDenormalizer extends ObjectDenormalizer
{
    use TimezoneFixerHelperTrait;

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress PossiblyNullReference - getPackage() can only return
     * null when a rate plan is created. It does not return null here.
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        /** @var \Apigee\Edge\Api\ApigeeX\Entity\AcceptedRatePlanInterface $denormalized */
        $denormalized = parent::denormalize($data, $type, $format, $context);

        return $denormalized;
    }

    /**
     * {@inheritdoc}
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
