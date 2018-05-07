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

use Apigee\Edge\Entity\EntityTransformer;
use Apigee\Edge\HttpClient\ClientInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class AbstractEntityController.
 *
 * Base controller for communicating Apigee Edge endpoints that accepts and returns data that can be serialized and
 * deserialized as entities.
 */
abstract class AbstractEntityController extends AbstractController
{
    /**
     * @var \Apigee\Edge\Entity\EntityTransformerInterface
     */
    protected $entityTransformer;

    /**
     * AbstractEntityController constructor.
     *
     * @param \Apigee\Edge\HttpClient\ClientInterface $client
     *   Apigee Edge API client.
     * @param \Symfony\Component\Serializer\Normalizer\NormalizerInterface[]|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface[] $entityNormalizers
     *   Array of entity normalizers and denormalizers that are being called earlier than the default ones.
     */
    public function __construct(ClientInterface $client, array $entityNormalizers = [])
    {
        parent::__construct($client);
        $this->entityTransformer = new EntityTransformer($entityNormalizers);
    }

    /**
     * Returns the entity type specific base url for an API call.
     *
     * @param string $entityId
     *
     * @return \Psr\Http\Message\UriInterface
     */
    protected function getEntityEndpointUri(string $entityId): UriInterface
    {
        return $this->getBaseEndpointUri()->withPath(sprintf('%s/%s', $this->getBaseEndpointUri(), $entityId));
    }

    /**
     * Returns the fully-qualified class name of the entity that this controller works with.
     *
     * @return string
     */
    abstract protected function getEntityClass(): string;
}
