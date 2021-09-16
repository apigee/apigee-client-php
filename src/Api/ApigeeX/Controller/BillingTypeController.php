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

use Apigee\Edge\Api\ApigeeX\Entity\BillingTypeInterface;
use Apigee\Edge\Api\ApigeeX\Serializer\BillingTypeSerializer;
use Apigee\Edge\Api\Monetization\Controller\EntityController;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Controller\EntityLoadOperationControllerTrait;
use Apigee\Edge\Serializer\EntitySerializerInterface;

abstract class BillingTypeController extends EntityController implements BillingTypeControllerInterface
{
    use EntityListingControllerTrait;
    use EntityLoadOperationControllerTrait;
    use PaginatedListingHelperTrait;

    /**
     * BillingTypeController constructor.
     *
     * @param string $organization
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $organization, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        $entitySerializer = $entitySerializer ?? new BillingTypeSerializer();
        parent::__construct($organization, $client, $entitySerializer);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllBillingDetails(): BillingTypeInterface
    {
        return $this->getDeveloperBillingType();
    }

    /**
     * {@inheritdoc}
     */
    public function updateBillingType($billingtype): BillingTypeInterface
    {
        $response = $this->client->put(
            $this->getBaseEndpointUri(),
            (string) json_encode((object) [
                'billingType' => $billingtype,
            ])
        );

        return $this->entitySerializer->deserialize(
            (string) $response->getBody(),
            $this->getEntityClass(),
            'json'
        );
    }

    /**
     * Helper function for getting the billing type.
     *
     * @return \Apigee\Edge\Api\ApigeeX\Entity\BillingTypeInterface
     */
    private function getDeveloperBillingType(): BillingTypeInterface
    {
        $item = $this->getRawSingleList($this->getBaseEndpointUri());

        /** @var \Apigee\Edge\Entity\EntityInterface $tmp */
        $tmp = $this->getEntitySerializer()->denormalize(
            $item,
            BillingTypeInterface::class,
            'json'
        );

        return $tmp;
    }
}
