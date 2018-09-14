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

use Apigee\Edge\Api\Monetization\Entity\CompanyAcceptedRatePlan;
use Apigee\Edge\Api\Monetization\Normalizer\EntityNormalizer;
use Apigee\Edge\Api\Monetization\Serializer\CompanyAcceptedRatePlanSerializer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\UriInterface;

class CompanyAcceptedRatePlanController extends AcceptedRatePlanController
{
    /**
     * Name of the company.
     *
     * @var string
     */
    protected $company;

    /**
     * CompanyAcceptedRatePlanController constructor.
     *
     * @param string $company
     * @param string $organization
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $company, string $organization, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        $entitySerializer = $entitySerializer ?? new CompanyAcceptedRatePlanSerializer();
        parent::__construct($organization, $client, $entitySerializer);
        $this->company = $company;
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/mint/organizations/{$this->organization}/companies/{$this->company}/developer-rateplans");
    }

    /**
     * @inheritdoc
     */
    protected function getEntityClass(): string
    {
        return CompanyAcceptedRatePlan::class;
    }

    /**
     * @inheritdoc
     */
    protected function buildContextForEntityTransformerInCreate(): array
    {
        $context = [];
        $context[EntityNormalizer::MINT_ENTITY_REFERENCE_PROPERTY_VALUES]['developer'] = $this->company;

        return $context;
    }

    /**
     * @inheritdoc
     */
    protected function getActiveRatePlansEndpoint(): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/mint/organizations/{$this->organization}/companies/{$this->company}/developer-accepted-rateplans");
    }

    /**
     * @inheritDoc
     */
    protected function getActiveRatePlanForApiProductEndpoint(string $apiProductName): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/mint/organizations/{$this->organization}/companies/{$this->company}/products/{$apiProductName}/rate-plan-by-developer-product");
    }
}
