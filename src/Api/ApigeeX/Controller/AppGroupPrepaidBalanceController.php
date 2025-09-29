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

use Psr\Http\Message\UriInterface;

class AppGroupPrepaidBalanceController extends PrepaidBalanceController implements AppGroupPrepaidBalanceControllerInterface
{
    /**
     * Name of the appgroup.
     *
     * @var string
     */
    protected $appGroupName;

    /**
     * AppGroupPrepaidBalanceController constructor.
     *
     * @param string $appGroupName
     * @param string $organization
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $appGroupName, string $organization, \Apigee\Edge\ClientInterface $client, ?\Apigee\Edge\Serializer\EntitySerializerInterface $entitySerializer = null)
    {
        parent::__construct($organization, $client, $entitySerializer);
        $this->appGroupName = $appGroupName;
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/organizations/{$this->organization}/appgroups/{$this->appGroupName}/balance:credit");
    }

    /**
     * {@inheritdoc}
     */
    protected function getPrepaidBalanceEndpoint(): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/organizations/{$this->organization}/appgroups/{$this->appGroupName}/balance");
    }
}
