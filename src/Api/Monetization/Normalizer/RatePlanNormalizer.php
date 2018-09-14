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

use Apigee\Edge\Api\Monetization\Entity\DeveloperCategoryRatePlanInterface;
use Apigee\Edge\Api\Monetization\Entity\DeveloperRatePlanInterface;
use Apigee\Edge\Api\Monetization\Entity\RatePlanInterface;
use Apigee\Edge\Api\Monetization\Entity\StandardRatePlanInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class RatePlanNormalizer extends EntityNormalizer
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

        if ($object instanceof StandardRatePlanInterface) {
            $normalized->type = RatePlanInterface::TYPE_STANDARD;
        } elseif ($object instanceof DeveloperRatePlanInterface) {
            $normalized->type = RatePlanInterface::TYPE_DEVELOPER;
        } elseif ($object instanceof DeveloperCategoryRatePlanInterface) {
            $normalized->type = RatePlanInterface::TYPE_DEVELOPER_CATEGORY;
        }

        // Fix the start- and end date of the rate plan if the organization's
        // timezone is different from the default PHP timezone.
        /** @var \Apigee\Edge\Api\Monetization\Entity\RatePlanInterface $object */
        if (date_default_timezone_get() !== $object->getPackage()->getOrganization()->getTimezone()->getName()) {
            $dateDenormalizer = new DateTimeNormalizer(RatePlanInterface::DATE_FORMAT, $object->getPackage()->getOrganization()->getTimezone());
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
        return $data instanceof RatePlanInterface;
    }
}
