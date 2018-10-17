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

use Apigee\Edge\Api\Monetization\Entity\ApiProduct;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Controller\EntityLoadOperationControllerTrait;
use Psr\Http\Message\UriInterface;

class ApiProductController extends OrganizationAwareEntityController implements ApiProductControllerInterface
{
    use EntityListingControllerTrait;
    use EntityLoadOperationControllerTrait;
    use PaginatedEntityListingControllerAwareTrait;
    use PaginatedListingHelperTrait;

    /**
     * @inheritdoc
     */
    public function getEligibleProductsByDeveloper(string $entityId): array
    {
        return $this->getEligibleProducts('developers', $entityId);
    }

    /**
     * @inheritdoc
     */
    public function getEligibleProductsByCompany(string $entityId): array
    {
        return $this->getEligibleProducts('companies', $entityId);
    }

    /**
     * @inheritdoc
     */
    protected function getEntityClass(): string
    {
        return ApiProduct::class;
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/mint/organizations/{$this->organization}/products");
    }

    /**
     * Returns eligible products of a developer or a company.
     *
     * @param string $type
     *   Either "developers" or "companies".
     * @param string $entityId
     *   Id of a developer or company.
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\ApiProductInterface[]
     *   List of eligible API products.
     *
     * @psalm-suppress PossiblyNullArrayOffset id() is not null.
     */
    private function getEligibleProducts(string $type, string $entityId): array
    {
        $products = [];
        foreach ($this->getRawList($this->client->getUriFactory()->createUri("/mint/organizations/{$this->organization}/{$type}/{$entityId}/eligible-products")) as $item) {
            /** @var \Apigee\Edge\Api\Monetization\Entity\ApiProductInterface $product */
            $product = $this->entitySerializer->denormalize($item, $this->getEntityClass());
            $products[$product->id()] = $product;
        }

        return $products;
    }
}
