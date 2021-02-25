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

namespace Apigee\Edge\Api\Monetization\Controller;

use Apigee\Edge\Api\Monetization\Entity\ApiXProduct;
use Apigee\Edge\Api\Monetization\Entity\XRatePlanInterface;
use Apigee\Edge\Api\Monetization\Serializer\ApiXProductSerializer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Controller\EntityLoadOperationControllerTrait;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\UriInterface;

class ApiXProductController extends OrganizationAwareEntityController implements ApiXProductControllerInterface
{
    use EntityCreateOperationControllerTrait;
    use EntityDeleteOperationControllerTrait;
    use EntityListingControllerTrait;
    use EntityLoadOperationControllerTrait;
    use PaginatedEntityListingControllerAwareTrait;
    use PaginatedListingHelperTrait;

    /**
     * ApiXProductController constructor.
     *
     * @param string $organization
     * @param ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $organization, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        $entitySerializer = $entitySerializer ?? new ApiXProductSerializer();
        parent::__construct($organization, $client, $entitySerializer);
    }

    /**
     * @inheritdoc
     */
    public function getAvailableApiXProductsByDeveloper(string $developerId, bool $active = false, bool $allAvailable = true): array
    {
        return $this->getAvailablexApiProducts('developers', $developerId, $active, $allAvailable);
    }

    /**
     * @inheritdoc
     */
    public function getAvailableApiXProductsByCompany(string $company, bool $active = false, bool $allAvailable = true): array
    {
        return $this->getAvailableApiPackages('companies', $company, $active, $allAvailable);
    }

    /**
     * @inheritdoc
     */
    protected function getEntityClass(): string
    {
        return ApiXProduct::class;
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/organizations/{$this->organization}/apiproducts");
    }

    private function getAvailablexApiProducts(string $type, string $id, bool $active = false, bool $allAvailable = true): array
    {
        $id = rawurlencode($id);

        return $this->listEntities($this->client->getUriFactory()->createUri("/organizations/{$this->organization}/apiproducts")->withQuery(http_build_query([
            'expand' => $active ? 'true' : 'false',
        ])));
    }
}
