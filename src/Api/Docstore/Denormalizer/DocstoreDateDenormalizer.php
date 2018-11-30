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

namespace Apigee\Edge\Api\Docstore\Denormalizer;

use Apigee\Edge\Denormalizer\EdgeDateDenormalizer;

/**
 * Class DocstoreDateDenormalizer.
 */
class DocstoreDateDenormalizer extends EdgeDateDenormalizer
{
    /**
     * Docstore uses a different date format then one used by Edge.
     *
     * It uses the ISO-8601 format vs Edge uses epoch time in milliseconds
     *
     * @param mixed $data
     * @param string $class
     * @param null $format
     * @param array $context
     *
     * @return object|null
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return parent::denormalize(strtotime($data) * 1000, $class, $format, $context);
    }
}
