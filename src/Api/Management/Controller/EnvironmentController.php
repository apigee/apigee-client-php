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

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Api\Management\Entity\Environment;
use Apigee\Edge\Controller\EntityController;
use Apigee\Edge\Controller\EntityCrudOperationsControllerTrait;
use Apigee\Edge\Controller\EntityIdsListingControllerTrait;
use Apigee\Edge\Denormalizer\PropertiesPropertyDenormalizer;
use Apigee\Edge\HttpClient\ClientInterface;
use Apigee\Edge\Normalizer\PropertiesPropertyNormalizer;
use Psr\Http\Message\UriInterface;

/**
 * Class EnvironmentController.
 */
class EnvironmentController extends EntityController implements EnvironmentControllerInterface
{
    use EntityCrudOperationsControllerTrait;
    use EntityIdsListingControllerTrait;

    /**
     * EnvironmentController constructor.
     *
     * @param string $organization
     * @param \Apigee\Edge\HttpClient\ClientInterface $client
     * @param array $entityNormalizers
     */
    public function __construct(
        string $organization,
        ClientInterface $client,
        array $entityNormalizers = []
    ) {
        $entityNormalizers[] = new PropertiesPropertyNormalizer();
        $entityNormalizers[] = new PropertiesPropertyDenormalizer();
        parent::__construct($organization, $client, $entityNormalizers);
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()
            ->createUri(sprintf('/organizations/%s/environments', $this->organization));
    }

    /**
     * @inheritdoc
     */
    protected function getEntityClass(): string
    {
        return Environment::class;
    }
}
