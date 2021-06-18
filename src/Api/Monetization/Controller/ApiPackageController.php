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

use Apigee\Edge\Api\Monetization\Entity\ApiPackage;
use Apigee\Edge\Api\Monetization\Entity\RatePlanInterface;
use Apigee\Edge\Api\Monetization\Serializer\ApiPackageSerializer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Controller\EntityLoadOperationControllerTrait;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\UriInterface;

class ApiPackageController extends OrganizationAwareEntityController implements ApiPackageControllerInterface
{
    use EntityCreateOperationControllerTrait;
    use EntityDeleteOperationControllerTrait;
    use EntityListingControllerTrait;
    use EntityLoadOperationControllerTrait;
    use PaginatedEntityListingControllerAwareTrait;
    use PaginatedListingHelperTrait;

    /**
     * ApiPackageController constructor.
     *
     * @param string $organization
     * @param ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $organization, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        $entitySerializer = $entitySerializer ?? new ApiPackageSerializer();
        parent::__construct($organization, $client, $entitySerializer);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteProduct(string $apiPackageId, string $productId): void
    {
        $productId = rawurlencode($productId);
        $this->getClient()->delete(
            $this->getBaseEndpointUri()->withPath("{$this->getBaseEndpointUri()}/{$apiPackageId}/products/{$productId}")
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addProduct(string $apiPackageId, string $productId): void
    {
        $productId = rawurlencode($productId);
        $this->getClient()->post(
            $this->getBaseEndpointUri()->withPath(
                "{$this->getBaseEndpointUri()}/{$apiPackageId}/products/{$productId}"
            ),
            // We have to send an empty JSON object in the request body.
            '{}'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableApiPackagesByDeveloper(string $developerId, bool $active = false, bool $allAvailable = true): array
    {
        return $this->getAvailableApiPackages('developers', $developerId, $active, $allAvailable);
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableApiPackagesByCompany(string $company, bool $active = false, bool $allAvailable = true): array
    {
        return $this->getAvailableApiPackages('companies', $company, $active, $allAvailable);
    }

    /**
     * {@inheritdoc}
     */
    public function addProductWithRatePlan(string $apiPackageId, string $productId, RatePlanInterface $ratePlan): void
    {
        // TODO: Implement addProductWithRatePlan() method.
        // We need to figure out the correct payload for this in DRUP-302.
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityClass(): string
    {
        return ApiPackage::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/mint/organizations/{$this->organization}/monetization-packages");
    }

    private function getAvailableApiPackages(string $type, string $id, bool $active = false, bool $allAvailable = true): array
    {
        $id = rawurlencode($id);

        return $this->listEntities($this->client->getUriFactory()->createUri("/mint/organizations/{$this->organization}/{$type}/{$id}/monetization-packages")->withQuery(http_build_query([
            'current' => $active ? 'true' : 'false',
            'allAvailable' => $allAvailable ? 'true' : 'false',
        ])));
    }
}
