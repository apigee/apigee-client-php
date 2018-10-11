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

use Apigee\Edge\Api\Monetization\Controller\TermsAndConditionsController;
use Apigee\Edge\Client;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\HttpClient\Utility\Builder;
use Apigee\Edge\Tests\Test\HttpClient\Plugin\NullAuthentication;
use GuzzleHttp\Psr7\Response;
use Http\Mock\Client as HttpClient;

class TermsAndConditionsControllerTest extends OrganizationAwareEntityControllerValidator
{
    use EntityCreateOperationControllerValidatorTrait;
    use EntityLoadOperationControllerValidatorTrait;
    use EntityUpdateOperationControllerValidatorTrait;
    use EntityDeleteOperationControllerValidatorTrait;
    use TimezoneConversionValidatorTrait;

    public function testEntityListing(): void
    {
        $httpClient = new HttpClient();
        $httpClient->setDefaultResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode((object) [[]])));
        $client = new Client(new NullAuthentication(), null, [Client::CONFIG_HTTP_CLIENT_BUILDER => new Builder($httpClient)]);
        $obj = new TermsAndConditionsController(static::getOrganization(static::$client), $client);
        $obj->getEntities();
        $this->assertEquals('current=false&all=true', $client->getJournal()->getLastRequest()->getUri()->getQuery());
        $obj->getEntities(true);
        $this->assertEquals('current=true&all=true', $client->getJournal()->getLastRequest()->getUri()->getQuery());
        $obj->getPaginatedEntityList();
        $this->assertEquals('current=false&page=1', $client->getJournal()->getLastRequest()->getUri()->getQuery());
        $obj->getPaginatedEntityList(1, 2, true);
        $this->assertEquals('current=true&page=2&size=1', $client->getJournal()->getLastRequest()->getUri()->getQuery());
    }

    /**
     * @inheritdoc
     */
    protected static function getEntityController(): EntityControllerInterface
    {
        static $controller;
        if (null === $controller) {
            $controller = new TermsAndConditionsController(static::getOrganization(static::$client), static::$client);
        }

        return $controller;
    }
}
