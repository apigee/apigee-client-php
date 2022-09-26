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

namespace Apigee\Edge\Serializer;

use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder as BaseJsonEncoder;

/**
 * Wrapper around Symfony's JSON encoder.
 *
 * Ensures proper encoding and decoding of JSONs.
 */
final class JsonEncoder extends BaseJsonEncoder
{
    /**
     * JsonEncoder constructor.
     *
     * The encoder implementation is intentionally not swappable.
     *
     * @param \Apigee\Edge\Serializer\JsonDecode|null $decodingImpl
     */
    public function __construct(JsonDecode $decodingImpl = null)
    {
        $decodingImpl = $decodingImpl ?: new JsonDecode([JsonDecode::ASSOCIATIVE => true]);
        parent::__construct(new JsonEncode([JsonEncode::OPTIONS => JSON_PRESERVE_ZERO_FRACTION]), $decodingImpl);
    }
}
