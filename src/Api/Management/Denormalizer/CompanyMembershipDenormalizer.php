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

namespace Apigee\Edge\Api\Management\Denormalizer;

use Apigee\Edge\Api\Management\Structure\CompanyMembership;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Denormalizes company membership data.
 */
class CompanyMembershipDenormalizer implements DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $denormalized = [];
        // Ignore parent "developer" key.
        $data = reset($data);
        foreach ($data as $item) {
            $denormalized[$item->email] = $item->role ?? null;
        }

        return new CompanyMembership($denormalized);
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

        return CompanyMembership::class === $type || $type instanceof CompanyMembership;
    }
}
