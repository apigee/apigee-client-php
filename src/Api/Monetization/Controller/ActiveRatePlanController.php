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

namespace Apigee\Edge\Api\Monetization\Controller;

use Apigee\Edge\Api\Monetization\Entity\RatePlanInterface;
use Apigee\Edge\Api\Monetization\Serializer\RatePlanSerializer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\UriInterface;

/**
 * Base class for developer- and company active rate plans listing.
 */
abstract class ActiveRatePlanController extends OrganizationAwareEntityController implements ActiveRatePlanControllerInterface
{
    use EntityListingControllerTrait;
    use EntityListingControllerAwareTrait;
    use ListingHelperTrait;

    /**
     * ActiveRatePlanController constructor.
     *
     * @param string $organization
     * @param ClientInterface $client
     * @param EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $organization, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        $entitySerializer = $entitySerializer ?? new RatePlanSerializer();
        parent::__construct($organization, $client, $entitySerializer);
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveRatePlanByApiProduct(string $apiProductName, ?bool $showPrivate = null): RatePlanInterface
    {
        $uri = $this->getActiveRatePlanForApiProductEndpoint($apiProductName);
        if (null !== $showPrivate) {
            $uri = $uri->withQuery(http_build_query(['showPrivate' => $showPrivate ? 'true' : 'false']));
        }
        $response = $this->client->get($uri);

        return $this->getEntitySerializer()->deserialize(
            (string) $response->getBody(),
            RatePlanInterface::class,
            'json'
        );
    }

    /**
     * Returns the URI of the get an active rate plan for an API product endpoint.
     *
     * We have to introduce this because it is not regular that an entity
     * has more than one listing endpoint so getBaseEntityEndpoint() was
     * enough until this time.
     *
     * @param string $apiProductName
     *   Name of the API product.
     *
     * @return UriInterface
     */
    abstract protected function getActiveRatePlanForApiProductEndpoint(string $apiProductName): UriInterface;

    /**
     * {@inheritdoc}
     */
    protected function getEntityClass(): string
    {
        return RatePlanInterface::class;
    }
}
