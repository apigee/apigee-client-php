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

use Apigee\Edge\Api\Management\Entity\ApiProduct;
use Apigee\Edge\Api\Management\Serializer\ApiProductSerializer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityCrudOperationsControllerTrait;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Controller\PaginatedEntityController;
use Apigee\Edge\Controller\PaginatedEntityIdListingControllerTrait;
use Apigee\Edge\Controller\PaginatedEntityListingControllerTrait;
use Apigee\Edge\Controller\PaginationHelperTrait;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class ApiProductController.
 */
class ApiProductController extends PaginatedEntityController implements ApiProductControllerInterface
{
    use AttributesAwareEntityControllerTrait;
    use EntityCrudOperationsControllerTrait;
    use EntityListingControllerTrait;
    use PaginatedEntityListingControllerTrait;
    use PaginatedEntityIdListingControllerTrait;
    use PaginationHelperTrait;

    /**
     * ApiProductController constructor.
     *
     * @param string $organization
     * @param ClientInterface $client
     * @param EntitySerializerInterface|null $entitySerializer
     * @param OrganizationControllerInterface|null $organizationController
     */
    public function __construct(
        string $organization,
        ClientInterface $client,
        ?EntitySerializerInterface $entitySerializer = null,
        ?OrganizationControllerInterface $organizationController = null,
    ) {
        $entitySerializer = $entitySerializer ?? new ApiProductSerializer();
        parent::__construct($organization, $client, $entitySerializer, $organizationController);
    }

    /**
     * {@inheritdoc}
     */
    public function searchByAttribute(string $attributeName, string $attributeValue): array
    {
        $query_params = [
            'attributename' => $attributeName,
            'attributevalue' => $attributeValue,
        ];
        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($query_params));
        $response = $this->client->get($uri);

        return $this->responseToArray($response);
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/organizations/{$this->organization}/apiproducts");
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityClass(): string
    {
        return ApiProduct::class;
    }
}
