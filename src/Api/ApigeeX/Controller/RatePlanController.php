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

use Apigee\Edge\Api\ApigeeX\Entity\RatePlanInterface;
use Apigee\Edge\Api\ApigeeX\Entity\RatePlanRevisionInterface;
use Apigee\Edge\Api\ApigeeX\Serializer\RatePlanSerializer;
use Apigee\Edge\Api\Monetization\Controller\EntityCrudOperationsControllerTrait;
use Apigee\Edge\Api\Monetization\Controller\OrganizationAwareEntityController;
use Apigee\Edge\Api\Monetization\Normalizer\EntityNormalizer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\UriInterface;

class RatePlanController extends OrganizationAwareEntityController implements RatePlanControllerInterface
{
    use EntityCrudOperationsControllerTrait {
        buildEntityCreatePayload as private traitBuildContextForEntityTransformerInCreate;
    }
    use EntityListingControllerTrait;

    /**
     * @var string
     */
    protected $apiProduct;

    /**
     * RatePlanController constructor.
     *
     * @param string $apiProduct
     * @param string $organization
     * @param ClientInterface $client
     * @param EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $apiProduct, string $organization, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        $this->apiProduct = $apiProduct;
        $entitySerializer = $entitySerializer ?? new RatePlanSerializer();
        parent::__construct($organization, $client, $entitySerializer);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntities(?bool $showCurrentOnly = null, ?bool $showPrivate = null, ?bool $showStandardOnly = null): array
    {
        $query_params = [
            'expand' => 'true',
            'state' => 'PUBLISHED',
        ];

        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($query_params));
        $response = $this->client->get($uri);
        $responseArray = $this->responseToArray($response);
        // Ignore entity type key from response, ex.: product.
        $responseArray = reset($responseArray);

        // XProduct is not monetized and we skip it.
        if (empty($responseArray)) {
            return [];
        }

        return $this->responseArrayToArrayOfEntities($responseArray);
    }

    /**
     * {@inheritdoc}
     *
     * Use RatePlanRevisionBuilder that makes it easier way to create
     * new rate plan revisions.
     *
     * @psalm-suppress PossiblyNullArgument - id is not null in this context.
     */
    public function createNewRevision(RatePlanRevisionInterface $entity): void
    {
        $payload = $this->getEntitySerializer()->serialize($entity, 'json');
        $response = $this->getClient()->post($this->getEntityEndpointUri($entity->getPreviousRatePlanRevision()->id()) . '/revision', $payload);
        $this->getEntitySerializer()->setPropertiesFromResponse($response, $entity);
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/organizations/{$this->organization}/apiproducts/{$this->apiProduct}/rateplans");
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityClass(): string
    {
        return RatePlanInterface::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildEntityCreatePayload(EntityInterface $entity, array $context = []): string
    {
        $context[EntityNormalizer::MINT_ENTITY_REFERENCE_PROPERTY_VALUES]['monetizationPackage'] = $this->apiProduct;

        return $this->baseBuildEntityCreatePayload($entity, $context);
    }
}
