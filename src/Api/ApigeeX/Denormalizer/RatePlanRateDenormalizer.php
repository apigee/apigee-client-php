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

use Apigee\Edge\Api\Monetization\Structure\RatePlanRate;
use Apigee\Edge\Api\Monetization\Structure\RatePlanRateRateCard;
use Apigee\Edge\Api\Monetization\Structure\RatePlanRateRevShare;
use Apigee\Edge\Denormalizer\ObjectDenormalizer;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

class RatePlanRateDenormalizer extends ObjectDenormalizer
{
    protected $revShareClass = RatePlanRateRevShare::class;

    protected $rateCardClass = RatePlanRateRateCard::class;

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $denormalized = (array) $data;
        if (RatePlanRate::TYPE_REVSHARE === $data->type) {
            return parent::denormalize($denormalized, $this->revShareClass, $format, $context);
        } elseif (RatePlanRate::TYPE_RATECARD === $data->type) {
            return parent::denormalize($denormalized, $this->rateCardClass, $format, $context);
        }

        throw new UnexpectedValueException(sprintf('Unexpected rate plan rate type with "%s".', $data->type));
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        // Do not apply this on array objects. ArrayDenormalizer takes care of them.
        if ('[]' === substr($type, -2)) {
            return false;
        }

        return RatePlanRate::class === $type || $type instanceof RatePlanRate;
    }
}
