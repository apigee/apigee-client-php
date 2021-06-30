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

use Apigee\Edge\Api\Management\Controller\CompanyAppCredentialController;
use Apigee\Edge\Api\Management\Entity\AppInterface;
use Apigee\Edge\Api\Management\Entity\AppOwnerInterface;
use Apigee\Edge\Api\Management\Entity\CompanyInterface;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Api\Management\Entity\CompanyAppTestEntityProviderTrait;
use Apigee\Edge\Tests\Api\Management\Entity\CompanyTestEntityProviderTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationTestControllerTesterInterface;

/**
 * Class CompanyAppCredentialControllerTest.
 *
 * @group controller
 * @group management
 */
class CompanyAppCredentialControllerTest extends AppCredentialControllerTestBase
{
    use CompanyControllerAwareTestTrait;
    use CompanyAppControllerAwareTestTrait;
    use CompanyTestEntityProviderTrait;
    use CompanyAppTestEntityProviderTrait;

    /**
     * {@inheritdoc}
     */
    protected static function entityController(ClientInterface $client = null): EntityControllerTesterInterface
    {
        $client = $client ?? static::defaultAPIClient();

        return new EntityControllerTester(new CompanyAppCredentialController(static::defaultTestOrganization($client), static::$testAppOwner->id(), static::$testApp->id(), $client));
    }

    protected static function setupTestApp(AppOwnerInterface $appOwner): AppInterface
    {
        $app = static::getNewCompanyApp();
        static::companyAppController()->create($app);

        return $app;
    }

    protected static function setupTestAppOwner(): AppOwnerInterface
    {
        $company = static::getNewCompany();
        static::companyController()->create($company);

        return $company;
    }

    protected static function companyAppControllerCompanyAppOwner(): CompanyInterface
    {
        return static::$testAppOwner;
    }

    protected static function entityCreateOperationTestController(): EntityCreateOperationTestControllerTesterInterface
    {
        return new EntityCreateOperationControllerTester(static::companyAppController());
    }

    protected static function getNewEntity(): EntityInterface
    {
        return static::getNewCompanyApp();
    }

    protected static function appByOwnerController(): EntityControllerTesterInterface
    {
        return static::companyAppController();
    }
}
