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

use Apigee\Edge\Api\Management\Entity\Organization;
use Apigee\Edge\Api\Management\Serializer\OrganizationSerializer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\AbstractEntityController;
use Apigee\Edge\Controller\EntityCrudOperationsControllerTrait;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Controller\NonPaginatedEntityIdListingControllerTrait;
use Apigee\Edge\Controller\NonPaginatedEntityListingControllerTrait;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class OrganizationController.
 */
class OrganizationController extends AbstractEntityController implements OrganizationControllerInterface
{
    use EntityCrudOperationsControllerTrait;
    use EntityListingControllerTrait;
    use NonPaginatedEntityListingControllerTrait;
    use NonPaginatedEntityIdListingControllerTrait;

    /**
     * OrganizationController constructor.
     *
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        $entitySerializer = $entitySerializer ?? new OrganizationSerializer();
        parent::__construct($client, $entitySerializer);
    }

    /**
     * @inheritDoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri('/organizations');
    }

    /**
     * @inheritdoc
     */
    protected function getEntityClass(): string
    {
        return Organization::class;
    }
}
