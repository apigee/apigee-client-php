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

use Apigee\Edge\Api\Monetization\Entity\OrganizationProfile;
use Apigee\Edge\Api\Monetization\Entity\OrganizationProfileInterface;
use Apigee\Edge\Api\Monetization\Serializer\OrganizationProfileSerializer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\UriInterface;

class OrganizationProfileController extends EntityController implements OrganizationProfileControllerInterface
{
    use EntityLoadOperationControllerTrait;
    use EntityUpdateOperationControllerTrait;

    /**
     * OrganizationProfileController constructor.
     *
     * @param string $organization
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $organization, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        $entitySerializer = $entitySerializer ?? new OrganizationProfileSerializer();
        parent::__construct($organization, $client, $entitySerializer);
    }

    /**
     * @inheritdoc
     */
    public function load(): OrganizationProfileInterface
    {
        $response = $this->client->get($this->getEntityEndpointUri($this->organization));

        return $this->entitySerializer->deserialize(
            (string) $response->getBody(),
            $this->getEntityClass(),
            'json'
        );
    }

    /**
     * @inheritdoc
     */
    protected function getEntityClass(): string
    {
        return OrganizationProfile::class;
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri('/mint/organizations');
    }
}
