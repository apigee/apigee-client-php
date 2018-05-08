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

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\ClientInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class CompanyAppCredentialController.
 */
class CompanyAppCredentialController extends AppCredentialController implements CompanyAppCredentialControllerInterface
{
    /** @var string Company name. */
    protected $companyName;

    /** @var string App name. */
    protected $appName;

    /**
     * DeveloperAppCredentialController constructor.
     *
     * @param string $organization
     * @param string $companyName
     * @param string $appName
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Symfony\Component\Serializer\Normalizer\NormalizerInterface[]|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface[] $entityNormalizers
     */
    public function __construct(
        string $organization,
        string $companyName,
        string $appName,
        ClientInterface $client,
        array $entityNormalizers = []
    ) {
        $this->companyName = $companyName;
        parent::__construct($organization, $appName, $client, $entityNormalizers);
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()
            ->createUri(sprintf(
                '/organizations/%s/companies/%s/apps/%s',
                $this->organization,
                $this->companyName,
                $this->appName
            ));
    }

    /**
     * @inheritdoc
     */
    protected function getEntityEndpointUri(string $entityId): UriInterface
    {
        return $this->getBaseEndpointUri()->withPath(sprintf('%s/keys/%s', $this->getBaseEndpointUri(), $entityId));
    }
}
