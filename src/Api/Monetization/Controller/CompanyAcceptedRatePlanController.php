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
    protected $companyName;

    /**
     * CompanyAcceptedRatePlanController constructor.
     *
     * @param string $companyName
     * @param string $organization
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $companyName, string $organization, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        parent::__construct($organization, $client, $entitySerializer);
        $this->companyName = $companyName;
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        // For these API endpoint:
        // https://apidocs.apigee.com/monetize/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_or_company_id%7D/developer-rateplans (create)
        // https://apidocs.apigee.com/monetize/apis/put/organizations/%7Borg_name%7D/developers/%7Bdeveloper_id%7D/developer-rateplans/%7Bplan_id%7D (update)
        // https://apidocs.apigee.com/monetize/apis/put/organizations/%7Borg_name%7D/developers/%7Bdeveloper_id%7D/developer-rateplans/%7Bplan_id%7D (load)
        return $this->client->getUriFactory()->createUri("/mint/organizations/{$this->organization}/companies/{$this->companyName}/developer-rateplans");
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityClass(): string
    {
        return CompanyAcceptedRatePlan::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildContextForEntityTransformerInCreate(): array
    {
        $context = [];
        $context[EntityNormalizer::MINT_ENTITY_REFERENCE_PROPERTY_VALUES]['developer'] = $this->companyName;

        return $context;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAcceptedRatePlansEndpoint(): UriInterface
    {
        // For this API endpoint:
        // https://apidocs.apigee.com/monetize/apis/get/organizations/%7Borg_name%7D/developers/%7Bdeveloper_id%7D/developer-accepted-rateplans
        return $this->client->getUriFactory()->createUri("/mint/organizations/{$this->organization}/companies/{$this->companyName}/developer-accepted-rateplans");
    }

    /**
     * {@inheritdoc}
     */
    protected function getEligibleRatePlansEndpoint(): UriInterface
    {
        // For this API endpoint:
        // https://apidocs.apigee.com/docs/monetization/1/routes/mint/organizations/%7Borg_name%7D/companies/%7Bcompany_id%7D/eligible-products/get
        return $this->client->getUriFactory()->createUri("/mint/organizations/{$this->organization}/companies/{$this->companyName}/eligible-products");
    }
}
