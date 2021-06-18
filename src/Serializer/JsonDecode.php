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
     *
     * @psalm-suppress InvalidArgument
     * Required since symfony/serializer >= 4.2.0
     *
     * @see https://github.com/symfony/symfony/pull/28709
     */
    public function __construct(bool $associative = false, int $depth = 512)
    {
        parent::__construct($associative, $depth);
        // Following the same logic as in JsonEcode.
        $this->options = JSON_PRESERVE_ZERO_FRACTION;
    }

    /**
     * {@inheritdoc}
     */
    public function decode($data, $format, array $context = [])
    {
        $context['json_decode_options'] = empty($context['json_decode_options']) ? $this->options : $context['json_decode_options'];

        return parent::decode($data, $format, $context);
    }
}
