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

namespace Apigee\Edge\Denormalizer;

use Apigee\Edge\Structure\PropertiesProperty;

/**
 * Class PropertiesPropertyDenormalizer.
 */
class PropertiesPropertyDenormalizer extends KeyValueMapDenormalizer
{
    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        // Do not apply this on array objects. ArrayDenormalizer takes care of them.
        if ('[]' === substr($type, -2)) {
            return false;
        }

        return PropertiesProperty::class === $type || $type instanceof PropertiesProperty;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (is_object($data) && property_exists($data, 'property') && is_array($data->property)) {
            $flatten = [];
            foreach ($data->property as $property) {
                $flatten[$property->name] = $property->value;
            }
            $data = $flatten;
        }

        return parent::denormalize($data, $class, $format, $context);
    }
}
