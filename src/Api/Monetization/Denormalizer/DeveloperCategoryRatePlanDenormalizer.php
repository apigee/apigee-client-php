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
use Apigee\Edge\Api\Monetization\Entity\RatePlanInterface;

class DeveloperCategoryRatePlanDenormalizer extends RatePlanDenormalizer
{
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
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data->parentRatePlan)) {
            return parent::denormalize($data, $this->developerCategoryRatePlanRevisionClass, $format, $context);
        }

        return parent::denormalize($data, $this->developerCategoryRatePlanClass, $format, $context);
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

        if (parent::supportsDenormalization($data, $type, $format)) {
            return RatePlanInterface::TYPE_DEVELOPER_CATEGORY == $data->type;
        }

        return false;
    }
}
