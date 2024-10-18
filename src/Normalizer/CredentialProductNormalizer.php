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

namespace Apigee\Edge\Normalizer;

use Apigee\Edge\Structure\CredentialProductInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class CredentialProductNormalizer.
 */
class CredentialProductNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     *
     * @psalm-suppress InvalidReturnType Returning an object here is required
     * for creating a valid Apigee Edge request.
     */
    public function normalize($object, $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        /* @var \Apigee\Edge\Structure\CredentialProductInterface $object */
        $asObject = [
            'apiproduct' => $object->getApiproduct(),
            'status' => $object->getStatus(),
        ];

        // Need to convert to ArrayObject as symfony normalizer throws error for std object.
        // Need to set ARRAY_AS_PROPS flag as we need Entries to be accessed as properties.
        return new \ArrayObject($asObject, \ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof CredentialProductInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            CredentialProductInterface::class => true,
        ];
    }
}
