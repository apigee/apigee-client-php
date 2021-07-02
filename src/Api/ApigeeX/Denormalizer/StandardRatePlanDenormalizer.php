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

use Apigee\Edge\Api\ApigeeX\Entity\RatePlanInterface;
use Apigee\Edge\Api\ApigeeX\Entity\StandardRatePlan;
use Apigee\Edge\Api\ApigeeX\Entity\StandardRatePlanRevision;

class StandardRatePlanDenormalizer extends RatePlanDenormalizer
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
     * {@inheritdoc}
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        if (isset($data->parentRatePlan)) {
            return parent::denormalize($data, $this->standardRatePlanRevisionClass, $format, $context);
        }

        return parent::denormalize($data, $this->standardRatePlanClass, $format, $context);
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
            //return RatePlanInterface::TYPE_STANDARD == $data->type;
            return RatePlanInterface::TYPE_STANDARD == 'STANDARD';
        }

        return false;
    }
}
