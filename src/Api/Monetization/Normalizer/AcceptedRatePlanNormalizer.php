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

use Apigee\Edge\Api\Monetization\Entity\AcceptedRatePlanInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class AcceptedRatePlanNormalizer extends EntityNormalizer
{
    /**
     * @inheritDoc
     *
     * @psalm-suppress InvalidReturnType Returning an object here is required
     * for creating a valid Apigee Edge request.
     */
    public function normalize($object, $format = null, array $context = [])
    {
        /** @var object $normalized */
        $normalized = parent::normalize($object, $format, $context);
        // Fix the start- and end date of the rate plan if the organization's
        // timezone is different from the default PHP timezone.
        /** @var \Apigee\Edge\Api\Monetization\Entity\AcceptedRatePlanInterface $object */
        if (date_default_timezone_get() !== $object->getRatePlan()->getPackage()->getOrganization()->getTimezone()->getName()) {
            $dateDenormalizer = new DateTimeNormalizer(AcceptedRatePlanInterface::DATE_FORMAT, $object->getRatePlan()->getPackage()->getOrganization()->getTimezone());
            $normalized->startDate = $dateDenormalizer->normalize($object->getStartDate());
            if (property_exists($normalized, 'endDate')) {
                $normalized->endDate = $dateDenormalizer->normalize($object->getEndDate());
            }
        }

        return $normalized;
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof AcceptedRatePlanInterface;
    }
}
