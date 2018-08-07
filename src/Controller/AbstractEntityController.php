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

use Apigee\Edge\ClientInterface;
use Apigee\Edge\Serializer\EntitySerializer;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class AbstractEntityController.
 *
 * Base controller for communicating Apigee Edge endpoints that accepts and returns data that can be serialized and
 * deserialized as entities.
 */
abstract class AbstractEntityController extends AbstractController
{
    use EntitySerializerAwareTrait;
    use EntityEndpointAwareControllerTrait;
    use EntityClassAwareTrait;

    /**
     * @var \Apigee\Edge\Serializer\EntitySerializerInterface
     */
    protected $entitySerializer;

    /**
     * AbstractEntityController constructor.
     *
     * @param \Apigee\Edge\ClientInterface $client
     *   Apigee Edge API client.
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        parent::__construct($client);
        $this->entitySerializer = $entitySerializer ?? new EntitySerializer();
    }

    /**
     * @inheritdoc
     */
    protected function getEntityEndpointUri(string $entityId): UriInterface
    {
        return $this->getBaseEndpointUri()->withPath("{$this->getBaseEndpointUri()}/{$entityId}");
    }

    /**
     * @inheritDoc
     */
    protected function getEntitySerializer(): EntitySerializerInterface
    {
        return $this->entitySerializer;
    }
}
