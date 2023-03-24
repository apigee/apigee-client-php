<?php

/*
 * Copyright 2023 Google LLC
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

use Apigee\Edge\Api\Management\Controller\CompanyAppCredentialController;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class AppGroupAppCredentialController.
 */
class AppGroupAppCredentialController extends CompanyAppCredentialController
{
    /** @var string appgroup name. */
    protected $appGroup;

    /**
     * DeveloperAppCredentialController constructor.
     *
     * @param string $organization
     * @param string $appGroup
     * @param string $appName
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(
        string $organization,
        string $appGroup,
        string $appName,
        ClientInterface $client,
        ?EntitySerializerInterface $entitySerializer = null
    ) {
        $this->appGroup = $appGroup;
        parent::__construct($organization, $appGroup, $appName, $client, $entitySerializer);
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        $appName = rawurlencode($this->appName);

        return $this->client->getUriFactory()->createUri("/organizations/{$this->organization}/appgroups/{$this->appGroup}/apps/{$appName}");
    }
}
