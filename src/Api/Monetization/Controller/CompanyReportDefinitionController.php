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

use Apigee\Edge\Api\Monetization\Entity\CompanyReportDefinition;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Psr\Http\Message\UriInterface;

class CompanyReportDefinitionController extends LegalEntityReportDefinitionController implements CompanyReportDefinitionControllerInterface
{
    /**
     * @var string
     */
    protected $companyName;

    /**
     * CompanyReportDefinitionController constructor.
     *
     * @param string $developerId
     * @param string $organization
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Api\Monetization\Controller\ReportDefinitionControllerInterface|null $reportDefinitionController
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $developerId, string $organization, ClientInterface $client, ?ReportDefinitionControllerInterface $reportDefinitionController = null, ?EntitySerializerInterface $entitySerializer = null)
    {
        $this->companyName = $developerId;
        parent::__construct($organization, $client, $reportDefinitionController, $entitySerializer);
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/mint/organizations/{$this->organization}/companies/{$this->companyName}/report-definitions");
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityClass(): string
    {
        return CompanyReportDefinition::class;
    }
}
