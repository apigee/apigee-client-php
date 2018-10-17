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

use Apigee\Edge\Api\Monetization\Entity\RatePlanInterface;
use Apigee\Edge\Api\Monetization\Entity\RatePlanRevisionInterface;
use Apigee\Edge\Api\Monetization\Normalizer\EntityNormalizer;
use Apigee\Edge\Api\Monetization\Serializer\RatePlanSerializer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityListingControllerTrait;
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
    protected $apiPackage;

    /**
     * RatePlanController constructor.
     *
     * @param string $apiPackage
     * @param string $organization
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $apiPackage, string $organization, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        $this->apiPackage = $apiPackage;
        $entitySerializer = $entitySerializer ?? new RatePlanSerializer();
        parent::__construct($organization, $client, $entitySerializer);
    }

    /**
     * @inheritdoc
     */
    public function getEntities(?bool $showCurrentOnly = null, ?bool $showPrivate = null, ?bool $showStandardOnly = null): array
    {
        $query_params = [
            'current' => $showCurrentOnly,
            'showPrivate' => $showPrivate,
            'standard' => $showStandardOnly,
        ];

        foreach ($query_params as $param => $value) {
            if (null === $value) {
                unset($query_params[$param]);
            } else {
                $query_params[$param] = $value ? 'true' : 'false';
            }
        }

        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($query_params));
        $response = $this->client->get($uri);
        $responseArray = $this->responseToArray($response);
        // Ignore entity type key from response, ex.: product.
        $responseArray = reset($responseArray);

        return $this->responseArrayToArrayOfEntities($responseArray);
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/mint/organizations/{$this->organization}/monetization-packages/{$this->apiPackage}/rate-plans");
    }

    /**
     * @inheritdoc
     */
    protected function getEntityClass(): string
    {
        return RatePlanInterface::class;
    }

    /**
     * @inheritDoc
     */
    protected function buildEntityCreatePayload(\Apigee\Edge\Entity\EntityInterface $entity, array $context = []): string
    {
        $context[EntityNormalizer::MINT_ENTITY_REFERENCE_PROPERTY_VALUES]['monetizationPackage'] = $this->apiPackage;

        return $this->baseBuildEntityCreatePayload($entity, $context);
    }
}
