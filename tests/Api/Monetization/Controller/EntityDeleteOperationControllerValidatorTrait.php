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

use Apigee\Edge\Tests\Test\Controller\ClientAwareTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerAwareTrait;
use PHPUnit\Framework\Assert;

/**
 * Applicable to all entity controllers that implements
 * Apigee\Edge\Api\Monetization\Controller\EntityDeleteControllerOperationInterface.
 */
trait EntityDeleteOperationControllerValidatorTrait
{
    use EntityControllerAwareTrait;
    use ClientAwareTestTrait;
    use EntityIdAwareControllerTrait;

    public function testDelete(): void
    {
        /** @var \Apigee\Edge\Api\Monetization\Controller\EntityDeleteOperationControllerInterface $controller */
        $controller = static::getEntityController();
        $controller->delete($this->getEntityIdForTestDelete());
        // Because MINT tests are pure offline API tests the best thing that we
        // can do (in general) is to make sure that the DELETE API call is sent
        // to the right endpoint.
        Assert::assertEquals(200, static::getClient()->getJournal()->getLastResponse()->getStatusCode());
    }

    protected function getEntityIdForTestDelete(): string
    {
        return 'phpunit';
    }
}
