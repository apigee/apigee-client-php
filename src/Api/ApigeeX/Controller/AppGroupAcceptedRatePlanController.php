<?php

/*
 * Copyright 2025 Google LLC
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

use Apigee\Edge\Api\ApigeeX\Entity\AppGroupAcceptedRatePlan;
use Apigee\Edge\Api\Monetization\Normalizer\EntityNormalizer;
use Apigee\Edge\Api\ApigeeX\Serializer\AppGroupAcceptedRatePlanSerializer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\UriInterface;

class AppGroupAcceptedRatePlanController extends AcceptedRatePlanController
{
    /**
     * Name of the appgroup.
     *
     * @var string
     */
    protected $appGroupName;

    /**
     * AppGroupAcceptedRatePlanController constructor.
     *
     * @param string $appGroupName
     * @param string $organization
     * @param ClientInterface $client
     * @param EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $appGroupName, string $organization, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        $entitySerializer = $entitySerializer ?? new AppGroupAcceptedRatePlanSerializer();
        parent::__construct($organization, $client, $entitySerializer);
        $this->appGroupName = $appGroupName;
        $this->organization = $organization;
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        // @todo update the enpoints
        // For these API endpoint:
        // https://apidocs.apigee.com/monetize/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_or_company_id%7D/developer-rateplans (create)
        // https://apidocs.apigee.com/monetize/apis/put/organizations/%7Borg_name%7D/developers/%7Bdeveloper_id%7D/developer-rateplans/%7Bplan_id%7D (update)
        // https://apidocs.apigee.com/monetize/apis/put/organizations/%7Borg_name%7D/developers/%7Bdeveloper_id%7D/developer-rateplans/%7Bplan_id%7D (load)
        return $this->client->getUriFactory()->createUri("/organizations/{$this->organization}/appgroups/{$this->appGroupName}/subscriptions");
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityClass(): string
    {
        return AppGroupAcceptedRatePlan::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildContextForEntityTransformerInCreate(): array
    {
        $context = [];
        $context[EntityNormalizer::MINT_ENTITY_REFERENCE_PROPERTY_VALUES]['developer'] = $this->appGroupName;

        return $context;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAcceptedRatePlansEndpoint(): UriInterface
    {

        // @todo update the enpoints
        // For this API endpoint:
        // https://apidocs.apigee.com/monetize/apis/get/organizations/%7Borg_name%7D/developers/%7Bdeveloper_id%7D/developer-accepted-rateplans
        return $this->client->getUriFactory()->createUri("/organizations/{$this->organization}/appgroups/{$this->appGroupName}/subscriptions");
    }
}
