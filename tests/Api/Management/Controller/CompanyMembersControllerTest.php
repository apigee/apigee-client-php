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

use Apigee\Edge\Api\Management\Controller\CompanyMembersController;
use Apigee\Edge\Api\Management\Controller\CompanyMembersControllerInterface;
use Apigee\Edge\Api\Management\Structure\CompanyMembership;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Tests\Api\Management\Entity\CompanyTestEntityProviderTrait;
use Apigee\Edge\Tests\Api\Management\Entity\DeveloperTestEntityProviderTrait;
use Apigee\Edge\Tests\Test\Controller\ControllerTestBase;
use Apigee\Edge\Tests\Test\Controller\DefaultAPIClientAwareTrait;
use Apigee\Edge\Tests\Test\TestClientFactory;
use Apigee\Edge\Tests\Test\Utility\EntityStorage;
use Apigee\Edge\Tests\Test\Utility\MarkOnlineTestSkippedAwareTrait;

/**
 * Class CompanyMembersControllerTest.
 *
 * @group controller
 * @group management
 */
class CompanyMembersControllerTest extends ControllerTestBase
{
    use DefaultAPIClientAwareTrait;
    use CompanyControllerAwareTestTrait;
    use DeveloperControllerAwareTestTrait;
    use CompanyTestEntityProviderTrait;
    use DeveloperTestEntityProviderTrait;
    use MarkOnlineTestSkippedAwareTrait;

    protected const TEST_ROLE = 'simple member';

    /** @var \Apigee\Edge\Api\Management\Entity\CompanyInterface */
    protected static $testCompany;

    /** @var \Apigee\Edge\Api\Management\Entity\DeveloperInterface */
    protected static $testDeveloper1;

    /** @var \Apigee\Edge\Api\Management\Entity\DeveloperInterface */
    protected static $testDeveloper2;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$testCompany = static::getNewCompany();
        static::companyController()->create(static::$testCompany);
        static::$testDeveloper1 = static::getNewDeveloper();
        static::developerController()->create(static::$testDeveloper1);
        static::$testDeveloper2 = static::getNewDeveloper();
        static::developerController()->create(static::$testDeveloper2);
    }

    /**
     * {@inheritdoc}
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        EntityStorage::getInstance()->purgeCreatedEntities();
    }

    public function testCreateMembership(): void
    {
        $members = $this->getCompanyMembershipController()->setMembers(new CompanyMembership([static::$testDeveloper1->getEmail() => static::TEST_ROLE]));
        $this->assertTrue($members->isMember(static::$testDeveloper1->getEmail()));
        $this->assertEquals(static::TEST_ROLE, $members->getRole(static::$testDeveloper1->getEmail()));
    }

    /**
     * @depends testCreateMembership
     *
     * @group online
     */
    public function testUpdateMembership(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $role = 'not a simple member';
        $members = $this->getCompanyMembershipController()->setMembers(new CompanyMembership([static::$testDeveloper1->getEmail() => $role]));
        $this->assertTrue($members->isMember(static::$testDeveloper1->getEmail()));
        $this->assertEquals($role, $members->getRole(static::$testDeveloper1->getEmail()));
    }

    /**
     * @depends testCreateMembership
     */
    public function testDeleteMembership(): void
    {
        $this->getCompanyMembershipController()->removeMember(static::$testDeveloper1->getEmail());
        $members = $this->getCompanyMembershipController()->getMembers();
        $this->assertEmpty($members->getMembers());
    }

    /**
     * @group online
     */
    public function testDeleteMembershipWithEmptyMemberList(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $members = $this->getCompanyMembershipController()->setMembers(new CompanyMembership([static::$testDeveloper1->getEmail() => static::TEST_ROLE]));
        $this->assertTrue($members->isMember(static::$testDeveloper1->getEmail()));
        $this->assertEquals(static::TEST_ROLE, $members->getRole(static::$testDeveloper1->getEmail()));
        $members = $this->getCompanyMembershipController()->setMembers(new CompanyMembership());
        $this->assertEmpty($members->getMembers());
    }

    /**
     * @depends testDeleteMembershipWithEmptyMemberList
     *   To make sure company has no members.
     *
     * @group online
     */
    public function testAddMember(): void
    {
        static::markOnlineTestSkipped(__FUNCTION__);
        $this->getCompanyMembershipController()->setMembers(new CompanyMembership([static::$testDeveloper1->getEmail() => '']));
        // Add a new member.
        $this->getCompanyMembershipController()->setMembers(new CompanyMembership([static::$testDeveloper2->getEmail() => '']));
        $members = $this->getCompanyMembershipController()->getMembers();
        $this->assertTrue($members->isMember(static::$testDeveloper1->getEmail()));
        $this->assertTrue($members->isMember(static::$testDeveloper2->getEmail()));
    }

    /**
     * {@inheritdoc}
     */
    protected static function defaultAPIClient(): ClientInterface
    {
        return TestClientFactory::getClient();
    }

    protected function getCompanyMembershipController(): CompanyMembersControllerInterface
    {
        return new CompanyMembersController(static::$testCompany->id(), static::defaultTestOrganization(static::defaultAPIClient()), static::defaultAPIClient());
    }
}
