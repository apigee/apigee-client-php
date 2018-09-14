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

use Apigee\Edge\Api\Monetization\Entity\DeveloperCategoryRatePlan;
use Apigee\Edge\Api\Monetization\Entity\DeveloperCategoryRatePlanRevision;
use Apigee\Edge\Api\Monetization\Entity\DeveloperRatePlan;
use Apigee\Edge\Api\Monetization\Entity\DeveloperRatePlanRevision;
use Apigee\Edge\Api\Monetization\Entity\RatePlan;
use Apigee\Edge\Api\Monetization\Entity\RatePlanInterface;
use Apigee\Edge\Api\Monetization\Entity\StandardRatePlan;
use Apigee\Edge\Api\Monetization\Entity\StandardRatePlanRevision;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

/**
 * Dynamically denormalizes rate plan entities.
 */
class RatePlanDenormalizer extends EntityDenormalizer
{
    /**
     * Fully qualified class name of the standard rate plan entity.
     *
     * @var string
     */
    protected $standardRatePlanClass = StandardRatePlan::class;

    /**
     * Fully qualified class name of the standard rate plan revision entity.
     *
     * @var string
     */
    protected $standardRatePlanRevisionClass = StandardRatePlanRevision::class;

    /**
     * Fully qualified class name of the developer rate plan entity.
     *
     * @var string
     */
    protected $developerRatePlanClass = DeveloperRatePlan::class;

    /**
     * Fully qualified class name of the developer rate plan revision entity.
     *
     * @var string
     */
    protected $developerRatePlanRevisionClass = DeveloperRatePlanRevision::class;

    /**
     * Fully qualified class name of the developer category rate plan entity.
     *
     * @var string
     */
    protected $developerCategoryRatePlanClass = DeveloperCategoryRatePlan::class;

    /**
     * Fully qualified class name of the developer category rate plan revision entity.
     *
     * @var string
     */
    protected $developerCategoryRatePlanRevisionClass = DeveloperCategoryRatePlanRevision::class;

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        // To be able to transform date strings to proper date objects we need
        // the timezone, but it is only available on the organization profile
        // object. Luckily all rate plans have a reference to the parent
        // organization profile.
        $startDate = $data->startDate;
        $endDate = property_exists($data, 'endDate') ? $data->endDate : null;
        /* @var \Apigee\Edge\Api\Monetization\Entity\RatePlanInterface $entity */
        if (isset($data->parentRatePlan)) {
            if (RatePlan::TYPE_DEVELOPER == $data->type) {
                $entity = parent::denormalize($data, $this->developerRatePlanRevisionClass, $format, $context);
            } elseif (RatePlan::TYPE_DEVELOPER_CATEGORY == $data->type) {
                $entity = parent::denormalize($data, $this->developerCategoryRatePlanRevisionClass, $format, $context);
            } else {
                $entity = parent::denormalize($data, $this->standardRatePlanRevisionClass, $format, $context);
            }
        } else {
            if (RatePlan::TYPE_DEVELOPER == $data->type) {
                $entity = parent::denormalize($data, $this->developerRatePlanClass, $format, $context);
            } elseif (RatePlan::TYPE_DEVELOPER_CATEGORY == $data->type) {
                $entity = parent::denormalize($data, $this->developerCategoryRatePlanClass, $format, $context);
            } else {
                $entity = parent::denormalize($data, $this->standardRatePlanClass, $format, $context);
            }
        }

        // Fix the start- and end date of the rate plan if the organization's
        // timezone is different from the default PHP timezone.
        if (date_default_timezone_get() !== $entity->getOrganization()->getTimezone()->getName()) {
            $dateDenormalizer = new DateTimeNormalizer(RatePlanInterface::DATE_FORMAT, $entity->getOrganization()->getTimezone());
            $entity->setStartDate($dateDenormalizer->denormalize($startDate, \DateTimeImmutable::class));
            if (null !== $endDate) {
                $entity->setEndDate($dateDenormalizer->denormalize($endDate, \DateTimeImmutable::class));
            }
        }

        return $entity;
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

        return RatePlanInterface::class === $type || $type instanceof RatePlanInterface || in_array(RatePlanInterface::class, class_implements($type));
    }
}
