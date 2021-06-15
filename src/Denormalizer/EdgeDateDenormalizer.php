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

use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class EdgeDateDenormalizer.
 */
class EdgeDateDenormalizer implements DenormalizerInterface
{
    private static $supportedTypes = [
        \DateTimeInterface::class => true,
        \DateTimeImmutable::class => true,
        \DateTime::class => true,
    ];

    /** @var \Symfony\Component\Serializer\Normalizer\DateTimeNormalizer */
    private $normalizer;

    /**
     * EdgeDateDenormalizer constructor.
     */
    public function __construct()
    {
        $this->normalizer = new DateTimeNormalizer();
    }

    /**
     * @inheritdoc
     *
     * @return object|null
     *
     * @psalm-suppress ImplementedReturnTypeMismatch - We have to return null,
     * even if it not officially supported by the overridden class.
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        // Handle -1 in expiresAt property of AppCredential.
        if ($data < 0) {
            return null;
        }
        $context[$this->normalizer::FORMAT_KEY] = 'U';
        $context[$this->normalizer::TIMEZONE_KEY] = new \DateTimeZone('UTC');

        return $this->normalizer->denormalize(intval($data / 1000), $type, $format, $context);
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return isset(self::$supportedTypes[$type]);
    }
}
