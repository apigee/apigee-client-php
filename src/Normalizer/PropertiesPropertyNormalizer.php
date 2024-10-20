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

namespace Apigee\Edge\Normalizer;

use Apigee\Edge\Structure\PropertiesProperty;
use ArrayObject;

/**
 * Class PropertiesPropertyNormalizer.
 */
class PropertiesPropertyNormalizer extends KeyValueMapNormalizer
{
    /**
     * Transforms JSON representation of properties property to compatible with what Edge accepts.
     *
     * {@inheritdoc}
     *
     * @psalm-suppress InvalidReturnType Returning an object here is required
     * for creating a valid Apigee Edge request.
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $return = [
            'property' => parent::normalize($object, $format, $context),
        ];

        // convert to ArrayObject as symfony normalizer throws error for std object.
        // set ARRAY_AS_PROPS flag as we need entries to be accessed as properties.
        return new ArrayObject($return, ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof PropertiesProperty;
    }
}
