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

use Apigee\Edge\Api\Monetization\Entity\TermsAndConditionsInterface;
use Apigee\Edge\Api\Monetization\Structure\AcceptedTermsAndConditions;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class AcceptedTermsAndConditionsNormalizer extends EntityNormalizer
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

        // Fix the audit date of the accepted terms and conditions
        // if the organization's timezone is different from the default
        // PHP timezone.
        /** @var \Apigee\Edge\Api\Monetization\Structure\AcceptedTermsAndConditions $object */
        if (date_default_timezone_get() !== $object->getTnc()->getOrganization()->getTimezone()->getName()) {
            $dateDenormalizer = new DateTimeNormalizer(TermsAndConditionsInterface::DATE_FORMAT, $object->getTnc()->getOrganization()->getTimezone());
            $normalized->auditDate = $dateDenormalizer->normalize($object->getAuditDate());
        }

        return $normalized;
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof AcceptedTermsAndConditions;
    }
}
