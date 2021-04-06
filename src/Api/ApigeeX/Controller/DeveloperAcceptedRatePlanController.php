<?php

/*
 * Copyright 2021 Google LLC
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

namespace Apigee\Edge\Api\ApigeeX\Controller;

use Apigee\Edge\Api\ApigeeX\Entity\AcceptedRatePlanInterface;
use Apigee\Edge\Api\ApigeeX\Entity\DeveloperAcceptedRatePlan;
use Apigee\Edge\Api\Monetization\Normalizer\EntityNormalizer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\UriInterface;

class DeveloperAcceptedRatePlanController extends AcceptedRatePlanController
{
    /**
     * UUID or email address of a developer.
     *
     * @var string
     */
    protected $developer;

    /**
     * DeveloperAcceptedRatePlanController constructor.
     *
     * @param string $developerId
     * @param string $organization
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $developerId, string $organization, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        parent::__construct($organization, $client, $entitySerializer);
        $this->developer = $developerId;
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseEndpointUri(): UriInterface
    {

        $developerId = rawurlencode($this->developer);

        return $this->client->getUriFactory()->createUri("/organizations/{$this->organization}/developers/{$developerId}/subscriptions");
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityClass(): string
    {
        return DeveloperAcceptedRatePlan::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildContextForEntityTransformerInCreate(): array
    {
        $context = [];
        $context[EntityNormalizer::MINT_ENTITY_REFERENCE_PROPERTY_VALUES]['Developer'] = $this->developer;

        return $context;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAcceptedRatePlansEndpoint(): UriInterface
    {
        $developerId = rawurlencode($this->developer);

        return $this->client->getUriFactory()->createUri("/organizations/{$this->organization}/developers/{$developerId}/subscriptions");
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress UndefinedMethod - getDeveloper() exists on the annotated
     * interface.
     */
    protected function alterRequestPayload(array &$payload, AcceptedRatePlanInterface $acceptedRatePlan): void
    {
        /* @var \Apigee\Edge\Api\Monetization\Entity\XDeveloperAcceptedRatePlanInterface $acceptedRatePlan */
        // We should prefer developer email addresses over developer ids
        // (UUIDs) when we are communicating with the Monetization API.
        // @see https://github.com/apigee/apigee-client-php/issues/36
        $payload['developer']['id'] = $acceptedRatePlan->getDeveloper()->getEmail();
    }
}
