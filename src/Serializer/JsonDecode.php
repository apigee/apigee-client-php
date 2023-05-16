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
     * True to return the result as an associative array, false for a nested stdClass hierarchy.
     */
    public const ASSOCIATIVE = 'json_decode_associative';

    public const OPTIONS = 'json_decode_options';

    /**
     * Specifies the recursion depth.
     */
    public const RECURSION_DEPTH = 'json_decode_recursion_depth';

    private $defaultContext = [
        self::ASSOCIATIVE => false,
        self::OPTIONS => 0,
        self::RECURSION_DEPTH => 512,
    ];

    /**
     * @var int
     */
    private $options;

    /**
     * JsonDecode constructor.
     *
     * @param array $defaultContext
     * @param int $depth
     */
    public function __construct($defaultContext = [], int $depth = 512)
    {
        if (!\is_array($defaultContext)) {
            @trigger_error(sprintf('Using constructor parameters that are not a default context is deprecated since Symfony 4.2, use the "%s" and "%s" keys of the context instead.', self::ASSOCIATIVE, self::RECURSION_DEPTH), \E_USER_DEPRECATED);

            $defaultContext = [
                self::ASSOCIATIVE => (bool) $defaultContext,
                self::RECURSION_DEPTH => $depth,
            ];
        }

        parent::__construct($defaultContext, $depth);
        // Following the same logic as in JsonEcode.
        $this->options = JSON_PRESERVE_ZERO_FRACTION;
    }

    /**
     * {@inheritdoc}
     */
    public function decode($data, $format, array $context = []): mixed
    {
        $context['json_decode_options'] = empty($context['json_decode_options']) ? $this->options : $context['json_decode_options'];

        return parent::decode($data, $format, $context);
    }
}
