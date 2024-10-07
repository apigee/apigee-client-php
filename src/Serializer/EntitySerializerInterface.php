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

use Apigee\Edge\Entity\EntityInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Serializes, deserializes, normalizes and denormalizes entities.
 */
/** @psalm-method bool supportsNormalization(mixed $data, ?string $format = null, array $context = []) */
interface EntitySerializerInterface extends NormalizerInterface, DenormalizerInterface, EncoderInterface, DecoderInterface, SerializerInterface
{
    /**
     * Set property values on an entity from an Apigee Edge response.
     *
     * @param ResponseInterface $response
     *   Response from Apigee Edge.
     * @param EntityInterface $entity
     *   Entity that properties should be updated.
     */
    public function setPropertiesFromResponse(ResponseInterface $response, EntityInterface $entity): void;
}
