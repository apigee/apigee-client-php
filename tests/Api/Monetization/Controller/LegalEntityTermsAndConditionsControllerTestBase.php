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

use Apigee\Edge\Api\Monetization\Entity\EntityInterface;
use Apigee\Edge\Api\Monetization\Structure\LegalEntityTermsAndConditionsHistoryItem;
use Apigee\Edge\Serializer\JsonEncoder;
use Apigee\Edge\Tests\Test\Controller\MockClientAwareTrait;
use Apigee\Edge\Tests\Test\HttpClient\FileSystemResponseFactory;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * Class LegalEntityTermsAndConditionsControllerTestBase.
 */
abstract class LegalEntityTermsAndConditionsControllerTestBase extends EntityControllerTestBase
{
    use MockClientAwareTrait;

    public function testGetTermsAndConditionsHistory(): void
    {
        /** @var \Apigee\Edge\Api\Monetization\Controller\LegalEntityTermsAndConditionsControllerInterface $controller */
        $controller = static::entityController();
        /** @var \Apigee\Edge\Api\Monetization\Structure\LegalEntityTermsAndConditionsHistoryItem[] $entities */
        $entities = $controller->getTermsAndConditionsHistory();
        $json = json_decode((string) static::defaultAPIClient()->getJournal()->getLastResponse()->getBody());
        $json = reset($json);
        $i = 0;
        foreach ($entities as $entity) {
            $this->assertEquals($json[$i]->id, $entity->getId());
            $this->assertEquals($json[$i]->action, $entity->getAction());
            // Timezone must be in UTC, always.
            // @see \Apigee\Edge\Api\Monetization\Structure\LegalEntityTermsAndConditionsHistoryItem::$auditDate
            $this->assertEquals('UTC', $entity->getAuditDate()->getTimezone()->getName());
            $this->assertEquals($json[$i]->auditDate, $entity->getAuditDate()->format(EntityInterface::DATE_FORMAT));
            // Validate the nested terms and conditions entity.
            $this->entitySerializerValidator()->validate($json[$i]->tnc, $entity->getTnc());
            ++$i;
        }
    }

    public function testAcceptDeclineTermsAndConditions(): void
    {
        $tncId = 'phpunit';
        /** @var \Apigee\Edge\Tests\Test\HttpClient\MockHttpClient $httpClient */
        $httpClient = static::mockApiClient()->getMockHttpClient();
        /** @var \Apigee\Edge\Api\Monetization\Controller\LegalEntityTermsAndConditionsControllerInterface $controller */
        $controller = static::entityController(static::mockApiClient());
        $encoder = new JsonEncoder();

        $mockResponse = $encoder->encode([
            // Action does not matter because we do not validate it.
            'action' => LegalEntityTermsAndConditionsHistoryItem::ACTION_ACCEPTED,
            'id' => static::randomGenerator()->machineName(),
            'auditDate' => static::entitySerializer()->normalize(new \DateTimeImmutable('now'), 'json'),
            'tnc' => $encoder->decode((string) (new FileSystemResponseFactory())->createResponseForRequest(new Request('GET', 'v1/mint/organizations/phpunit/tncs/phpunit'))->getBody(), 'json'),
            ], 'json');
        $httpClient->setDefaultResponse(new Response(200, ['Content-Type' => 'application/json'], $mockResponse));

        $controller->acceptTermsAndConditionsById($tncId);
        $this->assertEquals($this->expectedAcceptDeclineEndpoint(), static::mockApiClient()->getJournal()->getLastRequest()->getUri()->getPath());
        $payload = json_decode((string) static::mockApiClient()->getJournal()->getLastRequest()->getBody());
        $this->assertEquals(LegalEntityTermsAndConditionsHistoryItem::ACTION_ACCEPTED, $payload->action);
        $this->assertNotNull($payload->auditDate);

        $controller->declineTermsAndConditionsById($tncId);
        $this->assertEquals($this->expectedAcceptDeclineEndpoint(), static::mockApiClient()->getJournal()->getLastRequest()->getUri()->getPath());
        $payload = json_decode((string) static::mockApiClient()->getJournal()->getLastRequest()->getBody());
        $this->assertEquals(LegalEntityTermsAndConditionsHistoryItem::ACTION_DECLINED, $payload->action);
        $this->assertNotNull($payload->auditDate);
    }

    abstract protected function expectedAcceptDeclineEndpoint(): string;
}
