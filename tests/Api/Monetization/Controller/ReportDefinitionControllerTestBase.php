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

namespace Apigee\Edge\Tests\Api\Monetization\Controller;

use Apigee\Edge\Api\Monetization\Serializer\ReportDefinitionSerializer;
use Apigee\Edge\Api\Monetization\Structure\Reports\Criteria\AbstractCriteria;
use Apigee\Edge\Api\Monetization\Structure\Reports\Criteria\PrepaidBalanceReportCriteria;
use Apigee\Edge\Api\Monetization\Structure\Reports\Criteria\RevenueReportCriteria;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Apigee\Edge\Tests\Api\Monetization\Entity\ReportDefinitionEntityProviderTrait;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\ReportDefinitionSerializerValidator;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationTestControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityUpdateOperationControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\MockClientAwareTrait;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerValidatorInterface;
use Apigee\Edge\Tests\Test\TestClientFactory;
use GuzzleHttp\Psr7\Response;

abstract class ReportDefinitionControllerTestBase extends EntityControllerTestBase
{
    use MockClientAwareTrait;
    use ReportDefinitionEntityProviderTrait;
    // The order of these trait matters. Check @depends in test methods.
    use EntityCreateOperationControllerTestTrait;
    use EntityLoadOperationControllerTestTrait;
    use EntityUpdateOperationControllerTestTrait;
    use EntityDeleteOperationControllerTestTrait;

    public function testGenerateReport(): void
    {
        $test_org = static::defaultTestOrganization(static::mockApiClient());
        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = static::mockApiClient()->getMockHttpClient();
        $accepted_content_type = 'application/octet-stream';
        $httpClient->setDefaultResponse(new Response(200, ['Content-Type' => $accepted_content_type], 'foo,bar,baz'));
        /** @var \Apigee\Edge\Api\Monetization\Controller\ReportDefinitionControllerInterface $controller */
        $controller = static::entityController(static::mockApiClient());
        $controller->generateReport(static::getNewReportDefinition()->getCriteria());
        $this->assertEquals($accepted_content_type, static::mockApiClient()->getJournal()->getLastRequest()->getHeaderLine('Accept'));
        $this->assertEquals("/v1/mint/organizations/{$test_org}/billing-reports", static::mockApiClient()->getJournal()->getLastRequest()->getUri()->getPath());
        $controller->generateReport(new PrepaidBalanceReportCriteria('JANUARY', 2019));
        $this->assertEquals("/v1/mint/organizations/{$test_org}/prepaid-balance-reports", static::mockApiClient()->getJournal()->getLastRequest()->getUri()->getPath());
        $controller->generateReport(new RevenueReportCriteria(new \DateTimeImmutable('yesterday'), new \DateTimeImmutable('now')));
        $this->assertEquals("/v1/mint/organizations/{$test_org}/revenue-reports", static::mockApiClient()->getJournal()->getLastRequest()->getUri()->getPath());
    }

    /**
     * @expectedException \Apigee\Edge\Exception\InvalidArgumentException
     * @expectedExceptionMessage Unable to identify report type.
     */
    public function testGenerateReportWithUnknownCriteria(): void
    {
        $class = new class() extends AbstractCriteria {
        };
        /** @var \Apigee\Edge\Api\Monetization\Controller\ReportDefinitionControllerInterface $controller */
        $controller = static::entityController(static::mockApiClient());
        $controller->generateReport(new $class());
    }

    public function testFilteredEntities(): void
    {
        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = static::mockApiClient()->getMockHttpClient();
        $httpClient->setDefaultResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode((object) [[]])));
        /** @var \Apigee\Edge\Api\Monetization\Controller\ReportDefinitionControllerInterface $controller */
        $controller = static::entityController(static::mockApiClient());
        $controller->getFilteredEntities();
        $this->assertEquals($this->expectedFilteredReportDefinitionUrl(), static::mockApiClient()->getJournal()->getLastRequest()->getUri()->getPath());
        $this->assertEquals('page=1', static::mockApiClient()->getJournal()->getLastRequest()->getUri()->getQuery());
        $controller->getFilteredEntities(1, 2);
        $this->assertEquals('page=2&size=1', static::mockApiClient()->getJournal()->getLastRequest()->getUri()->getQuery());
        $controller->getFilteredEntities(1, 2, 'fooo');
        $this->assertEquals('page=2&size=1&sort=fooo', static::mockApiClient()->getJournal()->getLastRequest()->getUri()->getQuery());
    }

    abstract protected function expectedFilteredReportDefinitionUrl(): string;

    /**
     * @inheritDoc
     */
    protected function getEntitySerializer(): EntitySerializerInterface
    {
        static $validator;
        if (null === $validator) {
            $validator = new ReportDefinitionSerializer($this->entitySerializer());
        }

        return $validator;
    }

    /**
     * @inheritDoc
     */
    protected function entitySerializerValidator(): EntitySerializerValidatorInterface
    {
        static $validator;
        if (null === $validator) {
            $validator = new ReportDefinitionSerializerValidator($this->entitySerializer());
        }

        return $validator;
    }

    /**
     * @inheritDoc
     */
    protected static function entityCreateOperationTestController(): EntityCreateOperationTestControllerTesterInterface
    {
        return new EntityCreateOperationControllerTester(static::entityController());
    }

    /**
     * @inheritDoc
     */
    protected static function getNewEntity(): EntityInterface
    {
        return static::getNewReportDefinition(!TestClientFactory::isOfflineClient(static::defaultAPIClient()));
    }

    /**
     * @inheritDoc
     */
    protected function entityForUpdateTest(EntityInterface $existing): EntityInterface
    {
        return static::getUpdatedReportDefinition($existing, !TestClientFactory::isOfflineClient(static::defaultAPIClient()));
    }
}
