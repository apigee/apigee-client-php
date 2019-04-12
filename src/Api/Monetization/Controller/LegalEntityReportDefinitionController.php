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

use Apigee\Edge\Api\Monetization\Serializer\ReportDefinitionSerializer;
use Apigee\Edge\Api\Monetization\Structure\Reports\Criteria\AbstractCriteria;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Serializer\EntitySerializerInterface;

abstract class LegalEntityReportDefinitionController extends OrganizationAwareEntityController implements ReportDefinitionControllerInterface
{
    use EntityCreateOperationControllerTrait;
    use EntityListingControllerAwareTrait;
    use EntityListingControllerTrait;
    use FilteredReportDefinitionsTrait;

    /**
     * The trick is that only create- and list report definitions API endpoints
     * are available on the developer- and company specific report definition
     * API. Although you can use the base report definitions API endpoint to
     * view/update/delete a developer- or company specific report definition.
     * A listing API call does not return developer- or company specific
     * report definitions on the base report definitions API endpoint and
     * a developer- and company specific API call does not return
     * not developer- or company specific report definitions.
     *
     * @see https://apidocs.apigee.com/monetize/apis/get/organizations/%7Borg_name%7D/developers/%7Bdev_id%7D/report-definitions
     * @see https://apidocs.apigee.com/monetize/apis/post/organizations/%7Borg_name%7D/developers/%7Bdev_id%7D/report-definitions
     *
     * @var \Apigee\Edge\Api\Monetization\Controller\ReportDefinitionControllerInterface
     */
    protected $reportDefinitionController;

    /**
     * LegalEntityReportDefinitionController constructor.
     *
     * @param string $organization
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Api\Monetization\Controller\ReportDefinitionControllerInterface|null $reportDefinitionController
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $organization, ClientInterface $client, ?ReportDefinitionControllerInterface $reportDefinitionController = null, ?EntitySerializerInterface $entitySerializer = null)
    {
        $entitySerializer = $entitySerializer ?? new ReportDefinitionSerializer($organization);
        $this->reportDefinitionController = $reportDefinitionController ?? new ReportDefinitionController($organization, $client, $entitySerializer);
        parent::__construct($organization, $client, $entitySerializer);
    }

    /**
     * @inheritdoc
     */
    public function generateReport(AbstractCriteria $criteria): string
    {
        return $this->reportDefinitionController->generateReport($criteria);
    }

    /**
     * @inheritdoc
     */
    public function delete(string $entityId): void
    {
        $this->reportDefinitionController->delete($entityId);
    }

    /**
     * @inheritdoc
     */
    public function load(string $entityId): EntityInterface
    {
        return $this->reportDefinitionController->load($entityId);
    }

    /**
     * @inheritdoc
     */
    public function update(EntityInterface $entity): void
    {
        // @see \Apigee\Edge\Api\Monetization\Normalizer\LegalEntityReportDefinitionNormalizer::normalize()
        $this->reportDefinitionController->update($entity);
    }
}
