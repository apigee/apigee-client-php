<?php

/**
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or at
 * https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Serializer;

use Symfony\Component\Serializer\Encoder\JsonDecode as BaseJsonDecode;

/**
 * Wrapper around Symfony's JsonDecode.
 *
 * It ensures float numbers gets properly decoded.
 */
final class JsonDecode extends BaseJsonDecode
{
    /**
     * @var int
     */
    private $options;

    /**
     * JsonDecode constructor.
     *
     * @param bool $associative
     * @param int $depth
     */
    public function __construct(bool $associative = false, int $depth = 512)
    {
        parent::__construct($associative, $depth);
        // Following the same logic as in JsonEcode.
        $this->options = JSON_PRESERVE_ZERO_FRACTION;
    }

    /**
     * @inheritDoc
     */
    public function decode($data, $format, array $context = [])
    {
        $context['json_decode_options'] = empty($context['json_decode_options']) ? $this->options : $context['json_decode_options'];

        return parent::decode($data, $format, $context);
    }
}
