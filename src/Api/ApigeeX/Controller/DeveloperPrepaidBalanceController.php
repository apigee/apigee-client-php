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

use Apigee\Edge\ClientInterface;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\UriInterface;

class DeveloperPrepaidBalanceController extends PrepaidBalanceController implements DeveloperPrepaidBalanceControllerInterface
{
    /**
     * Email address of a developer.
     *
     * @var string
     */
    protected $developerId;

    /**
     * DeveloperPrepaidBalanceController constructor.
     *
     * @param string $developerId
     * @param string $organization
     * @param ClientInterface $client
     * @param EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $developerId, string $organization, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        parent::__construct($organization, $client, $entitySerializer);
        $this->developerId = $developerId;
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        $developerId = rawurlencode($this->developerId);

        return $this->client->getUriFactory()->createUri("/organizations/{$this->organization}/developers/{$developerId}/balance:credit");
    }

    /**
     * {@inheritdoc}
     */
    protected function getPrepaidBalanceEndpoint(): UriInterface
    {
        $developerId = rawurlencode($this->developerId);

        return $this->client->getUriFactory()->createUri("/organizations/{$this->organization}/developers/{$developerId}/balance");
    }
}
