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

use Apigee\Edge\Api\Monetization\Entity\TermsAndConditionsInterface;
use Apigee\Edge\Api\Monetization\Structure\AcceptedTermsAndConditions;
use Apigee\Edge\Denormalizer\ObjectDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class AcceptedTermsAndConditionsDenormalizer extends ObjectDenormalizer
{
    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        // To be able to transform date strings to proper date objects we need
        // the timezone, but it is only available on the organization profile
        // object. Luckily all accepted tncs have a reference to the parent
        // organization profile.
        $auditDate = $data->auditDate;

        /** @var \Apigee\Edge\Api\Monetization\Structure\AcceptedTermsAndConditions $entity */
        $entity = parent::denormalize($data, $class, $format, $context);

        // Fix the audit date of the accepted tnc.
        // @see \Apigee\Edge\Api\Monetization\Structure\AcceptedTermsAndConditions
        if ('UTC' !== date_default_timezone_get()) {
            $dateDenormalizer = new DateTimeNormalizer(TermsAndConditionsInterface::DATE_FORMAT, new \DateTimeZone('UTC'));
            $entity->setAuditDate($dateDenormalizer->denormalize($auditDate, \DateTimeImmutable::class));
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

        return AcceptedTermsAndConditions::class === $type || $type instanceof AcceptedTermsAndConditions;
    }
}
