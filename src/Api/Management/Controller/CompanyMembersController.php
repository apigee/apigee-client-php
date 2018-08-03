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

use Apigee\Edge\Api\Management\Denormalizer\CompanyMembershipDenormalizer;
use Apigee\Edge\Api\Management\Normalizer\CompanyMembershipNormalizer;
use Apigee\Edge\Api\Management\Structure\CompanyMembership;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\AbstractController;
use Apigee\Edge\Controller\OrganizationAwareControllerTrait;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Serializer;

/**
 * Allows to manage company memberships.
 */
class CompanyMembersController extends AbstractController implements CompanyMembersControllerInterface
{
    use CompanyAwareControllerTrait;
    use OrganizationAwareControllerTrait;

    /**
     * @var \Symfony\Component\Serializer\Serializer
     */
    protected $serializer;

    /** @var string */
    protected $organization;

    /**
     * CompanyMembersController constructor.
     *
     * @param string $companyName
     * @param string $organization
     * @param \Apigee\Edge\ClientInterface $client
     */
    public function __construct(string $companyName, string $organization, ClientInterface $client)
    {
        parent::__construct($client);
        $this->companyName = $companyName;
        $this->organization = $organization;
        $this->serializer = new Serializer([new CompanyMembershipDenormalizer(), new CompanyMembershipNormalizer()], [$this->jsonDecoder, new JsonEncode()]);
    }

    /**
     * @inheritdoc
     */
    public function getMembers(): CompanyMembership
    {
        $response = $this->client->get($this->getBaseEndpointUri());

        return $this->serializer->denormalize($this->responseToArray($response), CompanyMembership::class);
    }

    /**
     * @inheritdoc
     */
    public function setMembers(CompanyMembership $members): CompanyMembership
    {
        $response = $this->client->post($this->getBaseEndpointUri(), $this->serializer->serialize($members, 'json'));

        return $this->serializer->denormalize($this->responseToArray($response), CompanyMembership::class);
    }

    /**
     * @inheritdoc
     */
    public function removeMember(string $email): void
    {
        $this->client->delete($this->getBaseEndpointUri()->withPath("{$this->getBaseEndpointUri()->getPath()}/{$email}"));
    }

    /**
     * @inheritdoc
     */
    public function getOrganisationName(): string
    {
        return $this->organization;
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/organizations/{$this->organization}/companies/{$this->companyName}/developers");
    }
}
