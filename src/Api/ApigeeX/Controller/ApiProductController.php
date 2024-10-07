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

namespace Apigee\Edge\Api\ApigeeX\Controller;

use Apigee\Edge\Api\ApigeeX\Entity\ApiProduct;
use Apigee\Edge\Api\ApigeeX\Serializer\ApiProductSerializer;
use Apigee\Edge\Api\Monetization\Controller\EntityCreateOperationControllerTrait;
use Apigee\Edge\Api\Monetization\Controller\EntityDeleteOperationControllerTrait;
use Apigee\Edge\Api\Monetization\Controller\OrganizationAwareEntityController;
use Apigee\Edge\Api\Monetization\Controller\PaginatedEntityListingControllerAwareTrait;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Controller\EntityLoadOperationControllerTrait;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\UriInterface;

class ApiProductController extends OrganizationAwareEntityController implements ApiProductControllerInterface
{
    use EntityCreateOperationControllerTrait;
    use EntityDeleteOperationControllerTrait;
    use EntityListingControllerTrait;
    use EntityLoadOperationControllerTrait;
    use PaginatedEntityListingControllerAwareTrait;
    use PaginatedListingHelperTrait;

    /**
     * ApiProductController constructor.
     *
     * @param string $organization
     * @param ClientInterface $client
     * @param EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $organization, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        $entitySerializer = $entitySerializer ?? new ApiProductSerializer();
        parent::__construct($organization, $client, $entitySerializer);
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableApixProductsByDeveloper(string $developerId, bool $active = false, bool $allAvailable = true): array
    {
        return $this->getAvailablexApiProducts('developers', $developerId, $active, $allAvailable);
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableApixProductsByCompany(string $company, bool $active = false, bool $allAvailable = true): array
    {
        return $this->getAvailablexApiProducts('companies', $company, $active, $allAvailable);
    }

    /**
     * {@inheritdoc}
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\ApiProductInterface[]
     *
     * @psalm-return array<\Apigee\Edge\Api\Monetization\Entity\ApiProductInterface>
     */
    public function getEligibleProductsByDeveloper(string $entityId): array
    {
        return $this->getEligibleProducts('developers', $entityId);
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityClass(): string
    {
        return ApiProduct::class;
    }

    /**
     * {@inheritdoc}
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
        $entityId = rawurlencode($entityId);
        $products = [];

        $subscriptions = [];
        $subscribed_product_ids = [];
        if ('developers' == $type) {
            // Developer subscriptions.
            /** @var DeveloperAcceptedRatePlanController $dev_accepted_rateplan */
            $dev_accepted_rateplan = new DeveloperAcceptedRatePlanController($entityId, $this->organization, $this->client);
            $subscriptions = $dev_accepted_rateplan->getAllAcceptedRatePlans();

            foreach ($subscriptions as $subscription) {
                if (null === $subscription->getendTime()) {
                    $subscribed_product_ids[$subscription->getapiproduct()] = $subscription->getapiproduct();
                }
            }
        }

        $current_ms = substr((string) (microtime(true) * 1000), 0);

        foreach ($this->getAvailablexApiProducts($type, $entityId, true) as $item) {
            // Create a new rate plan controller.
            /** @var RatePlanController $rateplan */
            $rateplan = new RatePlanController($item->id(), $this->organization, $this->client);

            if (empty($rateplan->getEntities())) {
                // Free products - No rateplan.
                $products[$item->id()] = $item;
            } elseif (in_array($item->id(), $subscribed_product_ids)) {
                // Subscribed products.
                $products[$item->id()] = $item;
            } else {
                foreach ($rateplan->getEntities() as $plan) {
                    if (null !== $plan->getendTime() && $plan->getendTime() < $current_ms) {
                        // Free product - No active rateplan
                        $products[$item->id()] = $item;
                    }
                }
            }
        }

        return $products;
    }
}
