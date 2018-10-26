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

namespace Apigee\Edge\Tests\Api\Management\Controller;

use Apigee\Edge\Api\Management\Entity\CompanyInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Api\Management\Entity\CompanyAppTestEntityProviderTrait;
use Apigee\Edge\Tests\Api\Management\Entity\CompanyTestEntityProviderTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationTestControllerTesterInterface;
use Apigee\Edge\Tests\Test\TestClientFactory;

/**
 * Class CompanyAppControllerTest.
 *
 * @group controller
 * @group management
 */
class CompanyAppControllerTest extends AppControllerTestBase
{
    use CompanyAppControllerAwareTestTrait;
    use CompanyAppTestEntityProviderTrait;
    use CompanyControllerAwareTestTrait;
    use CompanyTestEntityProviderTrait;

    /** @var \Apigee\Edge\Api\Management\Entity\CompanyInterface */
    protected static $testCompany;

    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$testCompany = static::getNewCompany();
        static::companyController(static::defaultAPIClient())->create(static::$testCompany);
    }

    /**
     * @inheritdoc
     */
    protected static function entityController(): EntityControllerTesterInterface
    {
        return static::companyAppController();
    }

    /**
     * @inheritdoc
     */
    protected static function getNewEntity(): EntityInterface
    {
        return static::getNewCompanyApp([static::$testApiProduct->id()], !TestClientFactory::isOfflineClient(static::defaultAPIClient()));
    }

    /**
     * @inheritdoc
     */
    protected function entityForUpdateTest(EntityInterface $existing): EntityInterface
    {
        /* @var \Apigee\Edge\Api\Management\Entity\CompanyAppInterface $existing */
        return static::getUpdatedCompanyApp($existing, !TestClientFactory::isOfflineClient(static::defaultAPIClient()));
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
    protected static function companyAppControllerCompanyAppOwner(): CompanyInterface
    {
        return static::$testCompany;
    }

    /**
     * @inheritdoc
     */
    protected function reloadAppOwner()
    {
        return static::companyController()->load(static::$testCompany->id());
    }
}
