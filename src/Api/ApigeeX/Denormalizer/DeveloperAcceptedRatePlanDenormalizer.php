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

use Apigee\Edge\Api\ApigeeX\Entity\DeveloperAcceptedRatePlan;

class DeveloperAcceptedRatePlanDenormalizer extends AcceptedRatePlanDenormalizer
{
    /**
     * Fully qualified class name of the developer accepted rate plan entity.
     *
     * @var string
     */
    protected $developerAcceptedRatePlanClass = DeveloperAcceptedRatePlan::class;

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        return parent::denormalize($data, $this->developerAcceptedRatePlanClass, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        if (parent::supportsDenormalization($data, $type, $format)) {
            return true;
        }

        return false;
    }
}
