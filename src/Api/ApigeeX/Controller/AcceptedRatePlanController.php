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

use Apigee\Edge\Api\ApigeeX\Entity\AcceptedRatePlanInterface;
use Apigee\Edge\Api\ApigeeX\Entity\RatePlanInterface;
use Apigee\Edge\Api\ApigeeX\Serializer\AcceptedRatePlanSerializer;
use Apigee\Edge\Api\Monetization\Controller\OrganizationAwareEntityController;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Controller\EntityLoadOperationControllerTrait;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\UriInterface;
use ReflectionClass;

abstract class AcceptedRatePlanController extends OrganizationAwareEntityController implements AcceptedRatePlanControllerInterface
{
    use EntityListingControllerTrait;
    use EntityLoadOperationControllerTrait;
    use PaginatedListingHelperTrait;

    /**
     * AcceptedRatePlanController constructor.
     *
     * @param string $organization
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $organization, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        $entitySerializer = $entitySerializer ?? new AcceptedRatePlanSerializer();
        parent::__construct($organization, $client, $entitySerializer);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllAcceptedRatePlans(): array
    {
        return $this->getAcceptedRatePlans();
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginatedAcceptedRatePlanList(int $limit = null, int $page = 1): array
    {
        $query_params = [
            'page' => $page,
        ];

        if (null !== $limit) {
            $query_params['size'] = $limit;
        }

        return $this->getAcceptedRatePlans($query_params);
    }

    /**
     * {@inheritdoc}
     */
    public function acceptRatePlan(RatePlanInterface $ratePlan): AcceptedRatePlanInterface
    {
        $rc = new ReflectionClass($this->getEntityClass());
        /** @var \Apigee\Edge\Api\ApigeeX\Entity\AcceptedRatePlanInterface $acceptedRatePlan */
        $acceptedRatePlan = $rc->newInstance(
            [
                'ratePlan' => $ratePlan,
            ]
        );

        $payload = $this->getEntitySerializer()->serialize($acceptedRatePlan, 'json');

        $tmp = json_decode($payload, true);

        $payload = json_encode($tmp);

        $response = $this->client->post($this->getBaseEndpointUri(), $payload);
        $this->getEntitySerializer()->setPropertiesFromResponse($response, $acceptedRatePlan);

        return $acceptedRatePlan;
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress PossiblyNullArgument - id is not null in this context.
     */
    public function updateSubscription(AcceptedRatePlanInterface $acceptedRatePlan): void
    {
        $id = $acceptedRatePlan->getName();
        $response = $this->client->post($this->getEntityCancelEndpointUri($id));
    }

    /**
     * Builds context for the entity normalizer.
     *
     * Allows controllers to add extra metadata to the payload.
     *
     * @return array
     */
    abstract protected function buildContextForEntityTransformerInCreate(): array;

    /**
     * Returns the URI for listing accepted rate plans.
     *
     * We have to introduce this because it is not regular that an entity
     * has more than one listing endpoint so getBaseEntityEndpoint() was
     * enough until this time.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    abstract protected function getAcceptedRatePlansEndpoint(): UriInterface;

    /**
     * Allows to alter payload before it gets sent to the API.
     *
     * @param array $payload
     *   API request payload.
     */
    protected function alterRequestPayload(array &$payload, AcceptedRatePlanInterface $acceptedRatePlan): void
    {
    }

    /**
     * Helper function for listing accepted rate plans.
     *
     * @param array $query_params
     *   Additional query parameters.
     *
     * @return \Apigee\Edge\Api\ApigeeX\Entity\AcceptedRatePlanInterface[]
     *
     * @psalm-suppress PossiblyNullArrayOffset - id() does not return null here.
     */
    private function getAcceptedRatePlans(array $query_params = []): array
    {
        $entities = [];

        foreach ($this->getRawList($this->getAcceptedRatePlansEndpoint()->withQuery(http_build_query($query_params))) as $item) {
            // Added ID as name since in ApigeeX name field gives the id
            if (!isset($item->id)) {
                $item->id = $item->name ?? null;
            }

            /** @var \Apigee\Edge\Entity\EntityInterface $tmp */
            $tmp = $this->getEntitySerializer()->denormalize(
                $item,
                AcceptedRatePlanInterface::class,
                'json'
            );

            $entities[$tmp->id()] = $tmp;
        }

        return $entities;
    }
}
