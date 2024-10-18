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

namespace Apigee\Edge\Api\Management\Normalizer;

use Apigee\Edge\Api\Management\Entity\AppCredentialInterface;
use Apigee\Edge\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class AppCredentialNormalizer.
 */
class AppCredentialNormalizer extends ObjectNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof AppCredentialInterface;
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress InvalidReturnType Returning an object here is required
     * for creating a valid Apigee Edge request.
     */
    public function normalize($object, $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
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

    /**
     * {@inheritdoc}
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            AppCredentialInterface::class => true,
        ];
    }
}
