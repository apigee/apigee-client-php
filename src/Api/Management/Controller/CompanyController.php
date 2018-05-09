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

use Apigee\Edge\Api\Management\Entity\Company;
use Apigee\Edge\Controller\EntityController;
use Apigee\Edge\Controller\EntityCrudOperationsControllerTrait;
use Apigee\Edge\Controller\NonCpsListingEntityControllerTrait;
use Apigee\Edge\Controller\StatusAwareEntityControllerTrait;
use Apigee\Edge\Denormalizer\AttributesPropertyDenormalizer;
use Psr\Http\Message\UriInterface;

/**
 * Class CompanyController.
 */
class CompanyController extends EntityController implements CompanyControllerInterface
{
    use AttributesAwareEntityControllerTrait;
    use EntityCrudOperationsControllerTrait;
    use NonCpsListingEntityControllerTrait;
    use StatusAwareEntityControllerTrait;

    /**
     * CompanyController constructor.
     *
     * @param string $organization
     * @param \Apigee\Edge\ClientInterface $client
     * @param array $entityNormalizers
     */
    public function __construct(string $organization, \Apigee\Edge\ClientInterface $client, $entityNormalizers = [])
    {
        $entityNormalizers[] = new AttributesPropertyDenormalizer();
        parent::__construct($organization, $client, $entityNormalizers);
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/organizations/{$this->organization}/companies");
    }

    /**
     * @inheritdoc
     */
    protected function getEntityClass(): string
    {
        return Company::class;
    }
}
