<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Denormalizer;

use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class EdgeDateDenormalizer implements DenormalizerInterface
{
    private static $supportedTypes = [
        \DateTimeInterface::class => true,
        \DateTimeImmutable::class => true,
        \DateTime::class => true,
    ];

    private $normalizer;

    public function __construct()
    {
        $this->normalizer = new DateTimeNormalizer();
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        // Handle -1 in expiresAt property of AppCredential.
        if ($data < 0) {
            return null;
        }
        $context[$this->normalizer::FORMAT_KEY] = 'U';
        $context[$this->normalizer::TIMEZONE_KEY] = new \DateTimeZone('UTC');

        return $this->normalizer->denormalize(intval($data / 1000), $class, $format, $context);
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return isset(self::$supportedTypes[$type]);
    }
}
