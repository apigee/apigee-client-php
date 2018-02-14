<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\EntityNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class AppCredentialNormalizer.
 */
class AppCredentialNormalizer extends EntityNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof AppCredentialInterface;
    }

    /**
     * @inheritdoc
     *
     * @psalm-suppress InvalidReturnType Returning an object here is required
     * for creating a valid Apigee Edge request.
     */
    public function normalize($object, $format = null, array $context = [])
    {
        /** @var object $normalized */
        $normalized = parent::normalize($object, $format, $context);
        // Taking special care of null as a credential's expiresAt property value.
        // @see \Apigee\Edge\Api\Management\Entity\AppCredential::$expiresAt
        if (!isset($normalized->expiresAt)) {
            $normalized->expiresAt = -1;
        }

        return $normalized;
    }
}
