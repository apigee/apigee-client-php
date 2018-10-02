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

namespace Apigee\Edge\Api\Monetization\Builder;

use Apigee\Edge\Api\Monetization\Entity\RatePlanInterface;
use Apigee\Edge\Api\Monetization\Entity\RatePlanRevisionInterface;
use Apigee\Edge\Api\Monetization\Serializer\ApiPackageSerializer;
use Apigee\Edge\Api\Monetization\Serializer\EntitySerializer;
use Apigee\Edge\Api\Monetization\Serializer\OrganizationProfileSerializer;
use Apigee\Edge\Api\Monetization\Serializer\RatePlanSerializer;
use DateTimeImmutable;
use InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Utility class for creating new rate plan revisions more easily.
 *
 * @see https://docs.apigee.com/api-platform/monetization/create-future-rate-plans
 */
final class RatePlanRevisionBuilder
{
    /**
     * Creates a new rate plan revision object from an existing rate plan (revision).
     *
     * The end date of the new rate plan revision is not set even if it was
     * set on the "parent" rate plan.
     *
     * @param \Apigee\Edge\Api\Monetization\Entity\RatePlanInterface $ratePlan
     *   "Parent" rate plan.
     * @param \DateTimeImmutable $startDate
     *   Start date of the rate plan revision.
     *
     * @throws \InvalidArgumentException
     *   If the provided start date is earlier than the start date of the
     *   "parent" rate plan.
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\RatePlanRevisionInterface
     *   New rate plan revision.
     */
    public static function buildRatePlanRevision(RatePlanInterface $ratePlan, DateTimeImmutable $startDate): RatePlanRevisionInterface
    {
        if ($ratePlan->getStartDate()->getTimestamp() >= $startDate->getTimestamp()) {
            throw new InvalidArgumentException("Start date should not be earlier than parent rate plan's start date.");
        }
        $ratePlanSerializer = new RatePlanSerializer();
        // Normalize the parent rate plan to an array.
        // We have to normalize all "nested" entity references recursively
        // with their dedicated serializer because only their ids is being
        // returned by the rate plan serializer (as it should be) by default.
        /** @var object $normalized */
        $normalized = $ratePlanSerializer->normalize($ratePlan);
        $normalized->organization = (new OrganizationProfileSerializer())->normalize($ratePlan->getOrganization());
        $normalized->currency = (new EntitySerializer())->normalize($ratePlan->getCurrency());
        $normalized->currency->organization = $normalized->organization;
        $normalized->monetizationPackage = (new ApiPackageSerializer())->normalize($ratePlan->getPackage());
        $normalized->monetizationPackage->organization = $normalized->organization;
        // We do not need the "parent" rate plan of the "parent" rate plan for
        // this API call.
        if ($ratePlan instanceof RatePlanRevisionInterface) {
            unset($normalized->parentRatePlan);
        }
        // Set the "parent" rate plan for the new revision.
        $normalized->parentRatePlan = clone $normalized;
        // Id of the rate plan revision will be provided by Apigee Edge.
        unset($normalized->id);
        // Remove the end date inherited from the parent rate plan because
        // it may overlap with the new start date.
        unset($normalized->endDate);
        // Create a new rate plan revision from the "copy" of parent rate plan.
        // We have to disable the type enforcement because an empty "addresses"
        // array is being passed to the organization which causes failures.
        // @see Apigee\Edge\Api\Monetization\Entity\OrganizationAwareEntity
        // @see https://github.com/symfony/symfony/issues/28161
        /** @var \Apigee\Edge\Api\Monetization\Entity\RatePlanRevisionInterface $revision */
        $revision = $ratePlanSerializer->denormalize($normalized, RatePlanInterface::class, null, [ObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]);
        $revision->setStartDate($startDate);

        return $revision;
    }
}
