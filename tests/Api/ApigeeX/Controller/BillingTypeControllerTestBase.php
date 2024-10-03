<?php

/*
 * Copyright 2022 Google LLC
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

namespace Apigee\Edge\Tests\Api\ApigeeX\Controller;

use Apigee\Edge\Tests\Api\ApigeeX\EntitySerializer\BillingTypeSerializerValidator;
use Apigee\Edge\Tests\Test\Controller\MockClientAwareTrait;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerValidatorInterface;

abstract class BillingTypeControllerTestBase extends EntityControllerTestBase
{
    use MockClientAwareTrait;

    public function testGetAllBillingDetails(): void
    {
        /** @var \Apigee\Edge\Api\ApigeeX\Controller\BillingTypeControllerInterface $controller */
        $controller = static::entityController();
        $ratePlans = $controller->getAllBillingDetails();
        $input = json_decode((string) static::defaultAPIClient()->getJournal()->getLastResponse()->getBody());
        $this->entitySerializerValidator()->validate($input, $ratePlans);
    }

    public function testUpdateBillingType(): void
    {
        /** @var \Apigee\Edge\Api\ApigeeX\Controller\BillingTypeControllerInterface $acceptedController */
        $acceptedController = static::entityController(static::mockApiClient());

        // Response to updateBillingType().
        $billingtype = 'POSTPAID';
        $acceptedController->updateBillingType($billingtype);
        $payload = json_decode((string) static::mockApiClient()->getJournal()->getLastRequest()->getBody());
        $this->assertEquals($billingtype, $payload->billingType);

        // Response to updateBillingType().
        $billingtype = 'PREPAID';
        $acceptedController->updateBillingType($billingtype);
        $payload = json_decode((string) static::mockApiClient()->getJournal()->getLastRequest()->getBody());
        $this->assertEquals($billingtype, $payload->billingType);
    }

    /**
     * {@inheritdoc}
     */
    protected function entitySerializerValidator(): EntitySerializerValidatorInterface
    {
        static $validator;
        if (null === $validator) {
            $validator = new BillingTypeSerializerValidator($this->entitySerializer());
        }

        return $validator;
    }
}
