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

use Apigee\Edge\Api\Management\Controller\AppController;
use Apigee\Edge\Api\Management\Controller\AppControllerInterface;
use Apigee\Edge\Api\Management\Controller\DeveloperAppControllerInterface;
use Apigee\Edge\Api\Management\Entity\AppInterface;
use Apigee\Edge\Api\Management\Entity\CompanyAppInterface;
use Apigee\Edge\Api\Management\Entity\CompanyInterface;
use Apigee\Edge\Api\Management\Entity\DeveloperAppInterface;
use Apigee\Edge\Api\Management\Entity\DeveloperInterface;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Tests\Api\Management\Entity\CompanyAppTestEntityProviderTrait;
use Apigee\Edge\Tests\Api\Management\Entity\CompanyTestEntityProviderTrait;
use Apigee\Edge\Tests\Api\Management\Entity\DeveloperAppTestEntityProviderTrait;
use Apigee\Edge\Tests\Api\Management\Entity\DeveloperTestEntityProviderTrait;
use Apigee\Edge\Tests\Test\Controller\DefaultAPIClientAwareTrait;
use Apigee\Edge\Tests\Test\Controller\DefaultTestOrganizationAwareTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use Apigee\Edge\Tests\Test\Utility\MarkOnlineTestSkippedAwareTrait;

/**
 * Class AppControllerTest.
 *
 * @group controller
 * @group management
 */
class AppControllerTest extends EntityControllerTestBase
{
    use DeveloperControllerAwareTestTrait;
    use DeveloperAppControllerAwareTestTrait;
    use CompanyAppControllerAwareTestTrait;
    use CompanyControllerAwareTestTrait;
    use DeveloperTestEntityProviderTrait;
    use CompanyTestEntityProviderTrait;
    use DeveloperAppTestEntityProviderTrait;
    use CompanyAppTestEntityProviderTrait;
    use DefaultAPIClientAwareTrait;
    use DefaultTestOrganizationAwareTrait;
    use MarkOnlineTestSkippedAwareTrait;

    /** @var \Apigee\Edge\Api\Management\Entity\DeveloperInterface */
    protected static $testDeveloper;

    /** @var \Apigee\Edge\Api\Management\Entity\CompanyInterface */
    protected static $testCompany;

    /** @var \Apigee\Edge\Api\Management\Entity\DeveloperAppInterface */
    protected static $testDeveloperApp;

    /** @var \Apigee\Edge\Api\Management\Entity\CompanyAppInterface */
    protected static $testCompanyApp;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::markOnlineTestSkipped('AppControllerTest');
        static::$testDeveloper = static::getNewDeveloper();
        static::developerController()->create(static::$testDeveloper);
        static::$testCompany = static::getNewCompany();
        static::companyController()->create(static::$testCompany);
        static::$testDeveloperApp = static::getNewDeveloperApp();
        static::developerAppController()->create(static::$testDeveloperApp);
        static::$testCompanyApp = static::getNewCompanyApp();
        static::companyAppController()->create(static::$testCompanyApp);
    }

    public function testLoadApp(): void
    {
        $app = static::entityController()->loadApp(static::$testDeveloperApp->getAppId());
        $this->assertContains(DeveloperAppInterface::class, class_implements($app));
        $this->assertEquals(static::$testDeveloperApp, $app);
        $app = static::entityController()->loadApp(static::$testCompanyApp->getAppId());
        $this->assertContains(CompanyAppInterface::class, class_implements($app));
        $this->assertEquals(static::$testCompanyApp, $app);
    }

    public function testListAppIds(): void
    {
        $expected = [static::$testDeveloperApp->getAppId(), static::$testCompanyApp->getAppId()];
        $appIds = static::entityController()->listAppIds();
        foreach ($expected as $id) {
            $this->assertContains($id, $appIds);
        }
    }

    public function testListApps(): void
    {
        $apps = static::entityController()->listApps();
        $this->assertEquals(static::$testDeveloperApp, $apps[static::$testDeveloperApp->getAppId()]);
        $this->assertEquals(static::$testCompanyApp, $apps[static::$testCompanyApp->getAppId()]);
        $apps = static::entityController()->listApps(false);
        /** @var \Apigee\Edge\Api\Management\Entity\AppInterface $firstApp */
        $firstApp = reset($apps);
        $this->assertEmpty($firstApp->getCredentials());
    }

    public function testListAppIdsByStatus(): void
    {
        $approvedIDs = static::entityController()->listAppIdsByStatus(AppInterface::STATUS_APPROVED);
        $revokedIDs = static::entityController()->listAppIdsByStatus(AppInterface::STATUS_REVOKED);
        $this->assertContains(static::$testDeveloperApp->getAppId(), $approvedIDs);
        $this->assertContains(static::$testCompanyApp->getAppId(), $approvedIDs);
        $this->assertNotContains(static::$testDeveloperApp->getAppId(), $revokedIDs);
        $this->assertNotContains(static::$testCompanyApp->getAppId(), $revokedIDs);
        static::developerAppController()->setStatus(static::$testDeveloperApp->id(), DeveloperAppControllerInterface::STATUS_REVOKE);
        $approvedIDs = static::entityController()->listAppIdsByStatus(AppInterface::STATUS_APPROVED);
        $revokedIDs = static::entityController()->listAppIdsByStatus(AppInterface::STATUS_REVOKED);
        $this->assertNotContains(static::$testDeveloperApp->getAppId(), $approvedIDs);
        $this->assertContains(static::$testCompanyApp->getAppId(), $approvedIDs);
        $this->assertContains(static::$testDeveloperApp->getAppId(), $revokedIDs);
        $this->assertNotContains(static::$testCompanyApp->getAppId(), $revokedIDs);
    }

    public function testListAppIdsByType(): void
    {
        $developerAppIds = static::entityController()->listAppIdsByType(AppControllerInterface::APP_TYPE_DEVELOPER);
        $companyAppIds = static::entityController()->listAppIdsByType(AppControllerInterface::APP_TYPE_COMPANY);
        $this->assertContains(static::$testDeveloperApp->getAppId(), $developerAppIds);
        $this->assertContains(static::$testCompanyApp->getAppId(), $companyAppIds);
        $this->assertNotContains(static::$testDeveloperApp->getAppId(), $companyAppIds);
        $this->assertNotContains(static::$testCompanyApp->getAppId(), $developerAppIds);
    }

    /**
     * @covers \Apigee\Edge\Api\Management\Controller\AppController::listAppIdsByFamily
     *
     * @see https://docs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/appfamilies.
     */
    public function testListAppIdsByFamily(): void
    {
        $this->markTestSkipped('App families API is deprecated.');
    }

    /**
     * {@inheritdoc}
     */
    protected static function companyAppControllerCompanyAppOwner(): CompanyInterface
    {
        return static::$testCompany;
    }

    /**
     * {@inheritdoc}
     */
    protected static function developerAppControllerDeveloperAppOwner(): DeveloperInterface
    {
        return static::$testDeveloper;
    }

    /**
     * {@inheritdoc}
     *
     * @return \Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface|\Apigee\Edge\Api\Management\Controller\AppControllerInterface
     */
    protected static function entityController(ClientInterface $client = null): EntityControllerTesterInterface
    {
        $client = $client ?? static::defaultAPIClient();

        return new EntityControllerTester(new AppController(static::defaultTestOrganization($client), $client));
    }
}
