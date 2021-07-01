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

use Apigee\Edge\Api\Management\Entity\CompanyApp;
use Apigee\Edge\Api\Management\Serializer\AppEntitySerializer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class CompanyAppController.
 */
class CompanyAppController extends AppByOwnerController implements CompanyAppControllerInterface
{
    use CompanyAwareControllerTrait;
    /**
     * @var \Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface
     */
    protected $organizationController;

    /**
     * CompanyAppController constructor.
     *
     * @param string $organization
     * @param string $companyName
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     * @param \Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface|null $organizationController
     */
    public function __construct(
        string $organization,
        string $companyName,
        ClientInterface $client,
        ?EntitySerializerInterface $entitySerializer = null,
        ?OrganizationControllerInterface $organizationController = null
    ) {
        $this->companyName = $companyName;
        $entitySerializer = $entitySerializer ?? new AppEntitySerializer();
        $this->organizationController = $organizationController ?? new OrganizationController($client);
        parent::__construct($organization, $client, $entitySerializer);
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/organizations/{$this->organization}/companies/{$this->companyName}/apps");
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityClass(): string
    {
        return CompanyApp::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getOrganizationController(): OrganizationControllerInterface
    {
        return $this->organizationController;
    }
}
