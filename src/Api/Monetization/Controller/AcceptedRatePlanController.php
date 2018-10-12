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

use Apigee\Edge\Api\Monetization\Entity\AcceptedRatePlanInterface;
use Apigee\Edge\Api\Monetization\Entity\RatePlanInterface;
use Apigee\Edge\Api\Monetization\Serializer\AcceptedRatePlanSerializer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use DateTimeImmutable;
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
     * @inheritdoc
     */
    public function getAllAcceptedRatePlans(): array
    {
        return $this->getAcceptedRatePlans();
    }

    /**
     * @inheritdoc
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
     * @inheritDoc
     */
    public function acceptRatePlan(RatePlanInterface $ratePlan, DateTimeImmutable $startDate, ?DateTimeImmutable $endDate = null, ?int $quotaTarget = null, ?bool $suppressWarning = null, ?bool $waveTerminationCharge = null): AcceptedRatePlanInterface
    {
        $rc = new ReflectionClass($this->getEntityClass());
        /** @var \Apigee\Edge\Api\Monetization\Entity\AcceptedRatePlanInterface $acceptedRatePlan */
        $acceptedRatePlan = $rc->newInstance(
            [
                'ratePlan' => $ratePlan,
                'startDate' => $startDate,
            ]
        );
        if (null !== $quotaTarget) {
            $acceptedRatePlan->setQuotaTarget($quotaTarget);
        }
        if (null !== $endDate) {
            $acceptedRatePlan->setEndDate($endDate);
        }
        $payload = $this->getEntitySerializer()->serialize($acceptedRatePlan, 'json', $this->buildContextForEntityTransformerInCreate());
        $tmp = json_decode($payload, true);
        if (null !== $suppressWarning) {
            $tmp['suppressWarning'] = $suppressWarning ? 'true' : 'false';
        }
        if (null !== $waveTerminationCharge) {
            $tmp['waveTerminationCharge'] = $waveTerminationCharge ? 'true' : 'false';
        }
        $payload = json_encode($tmp);
        $response = $this->client->post($this->getBaseEndpointUri(), $payload);
        $this->getEntitySerializer()->setPropertiesFromResponse($response, $acceptedRatePlan);

        return $acceptedRatePlan;
    }

    /**
     * @inheritDoc
     *
     * @psalm-suppress PossiblyNullArgument - id is not null in this context.
     */
    public function updateSubscription(AcceptedRatePlanInterface $acceptedRatePlan, ?bool $suppressWarning = null, ?bool $waveTerminationCharge = null): void
    {
        $payload = $this->getEntitySerializer()->serialize($acceptedRatePlan, 'json', $this->buildContextForEntityTransformerInCreate());
        $tmp = json_decode($payload, true);
        if (null !== $suppressWarning) {
            $tmp['suppressWarning'] = $suppressWarning ? 'true' : 'false';
        }
        if (null !== $waveTerminationCharge) {
            $tmp['waveTerminationCharge'] = $waveTerminationCharge ? 'true' : 'false';
        }
        $payload = json_encode($tmp);
        // Update an existing entity.
        $response = $this->client->put($this->getEntityEndpointUri($acceptedRatePlan->id()), $payload);
        $this->getEntitySerializer()->setPropertiesFromResponse($response, $acceptedRatePlan);
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
     * Helper function for listing accepted rate plans.
     *
     * @param array $query_params
     *   Additional query parameters.
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\AcceptedRatePlanInterface[]
     *
     * @psalm-suppress PossiblyNullArrayOffset - id() does not return null here.
     */
    private function getAcceptedRatePlans(array $query_params = []): array
    {
        $entities = [];

        foreach ($this->getRawList($this->getAcceptedRatePlansEndpoint()->withQuery(http_build_query($query_params))) as $item) {
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
