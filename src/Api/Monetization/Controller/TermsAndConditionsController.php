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

use Apigee\Edge\Api\Monetization\Entity\TermsAndConditions;
use Apigee\Edge\Api\Monetization\Serializer\TermsAndConditionsSerializer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\UriInterface;

class TermsAndConditionsController extends OrganizationAwareEntityController implements TermsAndConditionsControllerInterface
{
    use EntityCrudOperationsControllerTrait;
    use EntityListingControllerTrait;
    use PaginatedListingHelperTrait;

    /**
     * TermsAndConditionsController constructor.
     *
     * @param string $organization
     * @param ClientInterface $client
     * @param EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $organization, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        $entitySerializer = $entitySerializer ?? new TermsAndConditionsSerializer();
        parent::__construct($organization, $client, $entitySerializer);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntities(?bool $currentOnly = null): array
    {
        $queryParams = [];
        if (null !== $currentOnly) {
            $queryParams = ['current' => $currentOnly ? 'true' : 'false'];
        }

        return $this->listAllEntities($this->getBaseEndpointUri()->withQuery(http_build_query($queryParams)));
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginatedEntityList(?int $limit = null, int $page = 1, ?bool $currentOnly = null): array
    {
        $queryParams = [];
        if (null !== $currentOnly) {
            $queryParams = ['current' => $currentOnly ? 'true' : 'false'];
        }

        return $this->listEntitiesInRange($this->getBaseEndpointUri()->withQuery(http_build_query($queryParams)), $limit, $page);
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/mint/organizations/{$this->organization}/tncs");
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityClass(): string
    {
        return TermsAndConditions::class;
    }
}
