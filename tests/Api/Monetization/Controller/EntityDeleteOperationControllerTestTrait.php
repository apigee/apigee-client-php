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

use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Test\Controller\DefaultAPIClientAwareTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerAwareTestTrait;
use PHPUnit\Framework\Assert;

/**
 * Monetization API version of EntityDeleteOperationControllerTestTrait.
 */
trait EntityDeleteOperationControllerTestTrait
{
    use DefaultAPIClientAwareTrait;
    use EntityControllerAwareTestTrait;

    /**
     * @depends testLoad
     *
     * @param EntityInterface $entity
     */
    public function testDelete(EntityInterface $entity): void
    {
        /** @var \Apigee\Edge\Api\Monetization\Controller\EntityDeleteOperationControllerInterface $controller */
        $controller = static::entityController();
        $controller->delete($entity->id());
        // Because MINT tests are pure offline API tests the best thing that we
        // can do (in general) is to make sure that the DELETE API call is sent
        // to the right endpoint.
        // TODO Maybe use the mock client to validate the path of the request.
        // This way we would not need an offline test file to validate this
        // API call.
        Assert::assertEquals(200, static::defaultAPIClient()->getJournal()->getLastResponse()->getStatusCode());
    }
}
