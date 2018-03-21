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

namespace Apigee\Edge\Controller;

use Apigee\Edge\Entity\EntityDenormalizer;
use Apigee\Edge\Entity\EntityFactory;
use Apigee\Edge\Entity\EntityFactoryInterface;
use Apigee\Edge\Entity\EntityNormalizer;
use Apigee\Edge\HttpClient\ClientInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * Class AbstractEntityController.
 *
 * Base controller for communicating Apigee Edge endpoints that accepts and returns data that can be serialized and
 * deserialized as entities.
 */
abstract class AbstractEntityController extends AbstractController
{
    /**
     * @var EntityFactoryInterface Entity factory that can return an entity which can be used as an internal
     * representation of the Apigee Edge API response.
     */
    protected $entityFactory;

    /**
     * @var \Symfony\Component\Serializer\Serializer
     */
    protected $entitySerializer;

    /**
     * AbstractEntityController constructor.
     *
     * @param ClientInterface|null $client
     *   API client.
     * @param EntityFactoryInterface|null $entityFactory
     *   Entity factory.
     * @param \Symfony\Component\Serializer\Normalizer\NormalizerInterface[] $entityNormalizers
     *   Array of entity normalizers and denormalizers that are being called earlier than the default ones.
     */
    public function __construct(ClientInterface $client = null, EntityFactoryInterface $entityFactory = null, array $entityNormalizers = [])
    {
        parent::__construct($client);
        $this->entityFactory = $entityFactory ?: new EntityFactory();
        $this->entitySerializer = new Serializer(
            array_merge($entityNormalizers, $this->entityNormalizers()),
            $this->entityEncoders()
        );
    }

    /**
     * Returns normalizers and denormalizers used by entity serializer for transforming entity data from/to objects.
     *
     * @return \Symfony\Component\Serializer\Normalizer\NormalizerInterface[]|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface[]
     */
    protected function entityNormalizers()
    {
        return [new EntityNormalizer(), new EntityDenormalizer()];
    }

    /**
     * Returns encoders and decoders used by entity serializer for reading and writing serialized entity data.
     *
     * @return \Symfony\Component\Serializer\Encoder\EncoderInterface[]|\Symfony\Component\Serializer\Encoder\DecoderInterface[]
     */
    protected function entityEncoders()
    {
        // Keep the same structure that we get from Edge, do not transforms objects to arrays.
        return [new JsonEncoder(null, new JsonDecode())];
    }

    /**
     * Returns the entity type specific base url for an API call.
     *
     * @param string $entityId
     *
     * @return UriInterface
     */
    protected function getEntityEndpointUri(string $entityId): UriInterface
    {
        return $this->getBaseEndpointUri()->withPath(sprintf('%s/%s', $this->getBaseEndpointUri(), $entityId));
    }
}
