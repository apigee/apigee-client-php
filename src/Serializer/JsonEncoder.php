<?php

/**
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or at
 * https://opensource.org/licenses/MIT.
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
        $decodingImpl = $decodingImpl ?: new JsonDecode(true);
        parent::__construct(new JsonEncode(JSON_PRESERVE_ZERO_FRACTION), $decodingImpl);
    }
}
