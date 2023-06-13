<?php

/*
 * Copyright 2023 Google LLC
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

use Apigee\Edge\Api\ApigeeX\Structure\AppGroupMembership;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Denormalizes appgroup membership data.
 */
class AppGroupMembershipDenormalizer implements DenormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $denormalized = [];
        if (!empty($data['attributes'])) {
            $data = $data['attributes'];
            // Getting the members email id and roles.
            $key = array_search('__apigee_reserved__developer_details', array_column($data, 'name'));
            $members = json_decode($data[$key]->value);
            if (is_array($members) || is_object($members)) {
                foreach ($members as $item) {
                    if (isset($item->developer)) {
                        $denormalized[$item->developer] = $item->roles ?? null;
                    }
                }
            }
        }

        return new AppGroupMembership($denormalized);
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

        return AppGroupMembership::class === $type || $type instanceof AppGroupMembership;
    }
}
