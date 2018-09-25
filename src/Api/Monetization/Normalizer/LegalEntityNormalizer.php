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

use Apigee\Edge\Api\Monetization\Entity\CompanyInterface;
use Apigee\Edge\Api\Monetization\Entity\DeveloperInterface;
use Apigee\Edge\Api\Monetization\Entity\LegalEntityInterface;

class LegalEntityNormalizer extends EntityNormalizer
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

        if ($object instanceof DeveloperInterface) {
            $normalized->isCompany = false;
        } elseif ($object instanceof CompanyInterface) {
            $normalized->isCompany = true;
        }
        // LegalEntityNameConverter extends OrganizationProfileNameConverter - according to our design pattern
        // (parent name converter should extend all nested entity references' name converts) therefore "broker"
        // automatically gets mapped to "hasBroker".
        // Name converters does not know what is the current "context" therefore it could not be solve in
        // the LegalEntityNameConverter to only map the "broker" (internal property name) to the "hasBroker"
        // (external property name) when the nester organization property gets serialized in a developer
        // or company entity.
        $normalized->broker = $object->isBroker();
        unset($normalized->hasBroker);

        return $normalized;
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof LegalEntityInterface;
    }
}
