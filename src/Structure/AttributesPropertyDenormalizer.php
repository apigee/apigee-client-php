<?php

/**
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

namespace Apigee\Edge\Structure;

/**
 * Class AttributesPropertyDenormalizer.
 */
class AttributesPropertyDenormalizer extends KeyValueMapDenormalizer
{
    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type instanceof AttributesProperty;
    }

    /**
     * Transforms input data to our internal representation.
     *
     * Acceptable inputs:
     *   - Edge response format: $data = [{'name' => 'foo', 'value' => 'bar'}, {'name' => 'bar', 'value' => 'baz'}]
     *     (from the EntityNormalizer for example)
     *   - Internal representation of attributes: ['foo' => 'bar', 'bar' => 'baz']
     *
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $flatten = [];
        foreach ($data as $key => $item) {
            if (is_object($item)) {
                // $data came from the EntityNormalizer.
                $flatten[$item->name] = $item->value;
            } else {
                $flatten[$key] = $item;
            }
        }
        $data = $flatten;

        return parent::denormalize($data, $class, $format, $context);
    }
}
