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

use Apigee\Edge\Api\Management\Entity\DeveloperApp;
use Apigee\Edge\ClientInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class DeveloperAppController.
 */
class DeveloperAppController extends AppByOwnerController implements DeveloperAppControllerInterface
{
    /**
     * @var string Developer email or id.
     */
    protected $developerId;

    /**
     * @var \Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface
     */
    protected $organizationController;

    /**
     * DeveloperAppController constructor.
     *
     * @param string $organization
     * @param string $developerId
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     * @param \Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface|null $organizationController
     */
    public function __construct(
        string $organization,
        string $developerId,
        ClientInterface $client,
        ?\Apigee\Edge\Serializer\EntitySerializerInterface $entitySerializer = null,
        ?OrganizationControllerInterface $organizationController = null
    ) {
        $this->developerId = $developerId;
        $this->organizationController = $organizationController ?? new OrganizationController($client);
        parent::__construct($organization, $client, $entitySerializer);
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        $developerId = rawurlencode($this->developerId);

        return $this->client->getUriFactory()->createUri("/organizations/{$this->organization}/developers/{$developerId}/apps");
    }

    /**
     * @inheritdoc
     */
    protected function getEntityClass(): string
    {
        return DeveloperApp::class;
    }

    /**
     * @inheritdoc
     */
    protected function getOrganizationController(): OrganizationControllerInterface
    {
        return $this->organizationController;
    }
}
