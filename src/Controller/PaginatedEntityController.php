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

namespace Apigee\Edge\Controller;

use Apigee\Edge\Api\Management\Controller\OrganizationController;
use Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Serializer\EntitySerializerInterface;

/**
 * Class PaginatedEntityController.
 *
 * @see \Apigee\Edge\Controller\PaginatedEntityControllerInterface
 */
abstract class PaginatedEntityController extends EntityController implements PaginatedEntityControllerInterface
{
    use OrganizationControllerAwareTrait;

    /** @var \Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface */
    protected $organizationController;

    /**
     * PaginatedEntityController constructor.
     *
     * @param string $organization
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     * @param OrganizationControllerInterface|null $organizationController
     */
    public function __construct(
        string $organization,
        ClientInterface $client,
        ?EntitySerializerInterface $entitySerializer = null,
        OrganizationControllerInterface $organizationController = null
    ) {
        parent::__construct($organization, $client, $entitySerializer);
        $this->organizationController = $organizationController ?: new OrganizationController($client);
    }

    /**
     * @inheritDoc
     */
    protected function getOrganizationController(): OrganizationControllerInterface
    {
        return $this->organizationController;
    }
}
