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

use Apigee\Edge\Api\Monetization\Entity\ReportDefinition;
use Apigee\Edge\Api\Monetization\Serializer\ReportDefinitionSerializer;
use Apigee\Edge\Api\Monetization\Structure\Reports\Criteria\AbstractCriteria;
use Apigee\Edge\Api\Monetization\Utility\ReportTypeFromCriteriaHelperTrait;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Apigee\Edge\Utility\ResponseToArrayHelper;
use Psr\Http\Message\UriInterface;

class ReportDefinitionController extends OrganizationAwareEntityController implements ReportDefinitionControllerInterface
{
    use EntityCrudOperationsControllerTrait;
    use EntityListingControllerAwareTrait;
    use EntityListingControllerTrait;
    use FilteredReportDefinitionsTrait;
    use ReportTypeFromCriteriaHelperTrait;
    use ResponseToArrayHelper;

    /**
     * ReportDefinitionController constructor.
     *
     * @param string $organization
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $organization, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        $entitySerializer = $entitySerializer ?? new ReportDefinitionSerializer($organization);
        parent::__construct($organization, $client, $entitySerializer);
    }

    /**
     * {@inheritdoc}
     */
    public function generateReport(AbstractCriteria $criteria): string
    {
        // Generate URL part from report type.
        $reportType = strtolower(str_replace('_', '-', $this->getReportTypeFromCriteria($criteria))) . '-reports';
        $response = $this->getClient()->post(
            $this->client->getUriFactory()->createUri("/mint/organizations/{$this->organization}/{$reportType}"),
            $this->getEntitySerializer()->serialize($criteria, 'json'),
            ['Accept' => 'application/octet-stream']
        );

        return (string) $response->getBody();
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/mint/organizations/{$this->organization}/report-definitions");
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityClass(): string
    {
        return ReportDefinition::class;
    }
}
