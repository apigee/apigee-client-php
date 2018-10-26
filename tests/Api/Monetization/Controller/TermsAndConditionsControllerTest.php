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
use Apigee\Edge\Api\Monetization\Entity\EntityInterface as MintEntityInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Api\Monetization\Entity\TermsAndConditionsEntityProviderTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationControllerTraitTest;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationTestControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityUpdateOperationControllerTestTrait;
use Apigee\Edge\Tests\Test\TestClientFactory;

/**
 * Class TermsAndConditionsControllerTest.
 *
 * @group controller
 * @group monetization
 */
class TermsAndConditionsControllerTest extends EntityControllerTestBase
{
    use TermsAndConditionsEntityProviderTrait;
    // The order of these trait matters. Check @depends in test methods.
    use EntityCreateOperationControllerTraitTest;
    use EntityLoadOperationControllerTestTrait;
    use EntityUpdateOperationControllerTestTrait;
    use EntityDeleteOperationControllerTestTrait;
    use TimezoneConversionTestTrait;

    /**
     * @inheritdoc
     */
    protected static function entityController(): EntityControllerTesterInterface
    {
        return new EntityControllerTester(new TermsAndConditionsController(static::defaultTestOrganization(static::defaultAPIClient()), static::defaultAPIClient()));
    }

    /**
     * @inheritdoc
     */
    protected static function entityCreateOperationTestController(): EntityCreateOperationTestControllerTesterInterface
    {
        return new EntityCreateOperationControllerTester(static::entityController());
    }

    /**
     * @inheritdoc
     */
    protected static function getNewEntity(): EntityInterface
    {
        return static::getNewTnC(!TestClientFactory::isOfflineClient(static::defaultAPIClient()));
    }

    /**
     * @inheritdoc
     */
    protected function entityForUpdateTest(EntityInterface $existing): EntityInterface
    {
        return static::getUpdatedTnC($existing, !TestClientFactory::isOfflineClient(static::defaultAPIClient()));
    }

    /**
     * @inheritdoc
     */
    protected function getTestEntityForTimezoneConversion(): MintEntityInterface
    {
        // This is fine for now.
        return static::controllerForEntityLoad()->load('phpunit');
    }
}
