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

use Apigee\Edge\Structure\CredentialProduct;
use Apigee\Edge\Structure\CredentialProductInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class CredentialProductDenormalizer.
 */
class CredentialProductDenormalizer implements DenormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        // Do not apply this on array objects. ArrayDenormalizer takes care of them.
        if ('[]' === substr($type, -2)) {
            return false;
        }

        return CredentialProductInterface::class === $type || $type instanceof CredentialProductInterface || in_array(CredentialProductInterface::class, class_implements($type));
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        return new CredentialProduct($data->apiproduct, $data->status);
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedTypes(?string $format): array {
        return [
            CredentialProductInterface::class => TRUE,
        ];
    }
}
