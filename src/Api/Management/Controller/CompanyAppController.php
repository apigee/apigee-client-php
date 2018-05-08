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

use Apigee\Edge\Api\Management\Entity\CompanyApp;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\CpsListingEntityControllerTrait;
use Apigee\Edge\Controller\EntityCrudOperationsControllerTrait;
use Apigee\Edge\Controller\StatusAwareEntityControllerTrait;
use Psr\Http\Message\UriInterface;

/**
 * Class CompanyAppController.
 */
class CompanyAppController extends AppByOwnerController
{
    use AttributesAwareEntityControllerTrait;
    use AppControllerTrait;
    use CpsListingEntityControllerTrait;
    use EntityCrudOperationsControllerTrait;
    use StatusAwareEntityControllerTrait;

    /**
     * Name of an company.
     *
     * @var string
     */
    protected $companyName;

    /**
     * CompanyAppController constructor.
     *
     * @param string $organization
     * @param string $companyName
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Symfony\Component\Serializer\Normalizer\NormalizerInterface[]|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface[] $entityNormalizers
     * @param \Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface|null $organizationController
     */
    public function __construct(
        string $organization,
        string $companyName,
        ClientInterface $client,
        array $entityNormalizers = [],
        ?OrganizationControllerInterface $organizationController = null
    ) {
        $this->companyName = $companyName;
        $entityNormalizers = array_merge($entityNormalizers, $this->appEntityNormalizers());
        parent::__construct($organization, $client, $entityNormalizers, $organizationController);
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()
            ->createUri(sprintf('/organizations/%s/companies/%s/apps', $this->organization, $this->companyName));
    }

    /**
     * @inheritdoc
     */
    protected function getEntityClass(): string
    {
        return CompanyApp::class;
    }
}
