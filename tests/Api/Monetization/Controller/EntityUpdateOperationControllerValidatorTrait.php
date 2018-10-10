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
use Apigee\Edge\Tests\Test\Controller\ClientAwareTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerAwareTrait;
use PHPUnit\Framework\Assert;

/**
 * Applicable to all entity controllers that implements
 * Apigee\Edge\Api\Monetization\Controller\EntityUpdateControllerOperationInterface.
 */
trait EntityUpdateOperationControllerValidatorTrait
{
    use EntityControllerAwareTrait;
    use ClientAwareTestTrait;
    use TestEntityIdAwareControllerValidatorTrait;

    public function testUpdate(): void
    {
        /** @var \Apigee\Edge\Api\Monetization\Controller\EntityUpdateControllerOperationInterface $controller */
        $controller = static::getEntityController();
        $controller->update($this->getEntityForTestUpdate());
        // Because MINT tests are pure offline API tests the best thing that we
        // can do (in general) is to make sure that the PUT API call is sent
        // to the right place. The testLoad() has already ensured that the
        // API client can serialize and deserialize an API response properly.
        // Classes that use this trait can also do extra validations on the
        // entity.
        Assert::assertEquals(200, static::getClient()->getJournal()->getLastResponse()->getStatusCode());
    }

    protected function getEntityForTestUpdate(): EntityInterface
    {
        return $this->getEntityController()->load($this->getTestEntityId());
    }
}
