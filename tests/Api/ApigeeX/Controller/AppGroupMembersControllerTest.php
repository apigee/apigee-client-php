<?php

/*
 * Copyright 2023 Google LLC
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

use Apigee\Edge\Api\ApigeeX\Controller\AppGroupMembersController;
use Apigee\Edge\Api\ApigeeX\Controller\AppGroupMembersControllerInterface;
use Apigee\Edge\Api\ApigeeX\Structure\AppGroupMembership;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Tests\Api\ApigeeX\Entity\AppGroupTestEntityProviderTrait;
use Apigee\Edge\Tests\Api\Management\Controller\DeveloperControllerAwareTestTrait;
use Apigee\Edge\Tests\Api\Management\Entity\DeveloperTestEntityProviderTrait;
use Apigee\Edge\Tests\Test\Controller\ControllerTestBase;
use Apigee\Edge\Tests\Test\Controller\DefaultAPIClientAwareTrait;
use Apigee\Edge\Tests\Test\TestClientFactory;
use Apigee\Edge\Tests\Test\Utility\EntityStorage;
use Apigee\Edge\Tests\Test\Utility\MarkOnlineTestSkippedAwareTrait;

/**
 * Class AppGroupMembersControllerTest.
 *
 * @group controller
 * @group management
 */
class AppGroupMembersControllerTest extends ControllerTestBase
{
    use DefaultAPIClientAwareTrait;
    use AppGroupControllerAwareTestTrait;
    use DeveloperControllerAwareTestTrait;
    use AppGroupTestEntityProviderTrait;
    use DeveloperTestEntityProviderTrait;
    use MarkOnlineTestSkippedAwareTrait;

    protected const TEST_ROLE = 'simple member';

    /** @var \Apigee\Edge\Api\ApigeeX\Entity\AppGroupInterface */
    protected static $testAppGroup;

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
        static::$testAppGroup = static::getNewAppGroup();
        static::appGroupController()->create(static::$testAppGroup);
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
        $members = $this->getAppGroupMembershipController()->setMembers(new AppGroupMembership([static::$testDeveloper1->getEmail() => static::TEST_ROLE]));
        $this->assertTrue($members->isMember(static::$testDeveloper1->getEmail()));
        $this->assertContains(static::TEST_ROLE, $members->getRole(static::$testDeveloper1->getEmail()));
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
        $members = $this->getAppGroupMembershipController()->setMembers(new AppGroupMembership([static::$testDeveloper1->getEmail() => $role]));
        $this->assertTrue($members->isMember(static::$testDeveloper1->getEmail()));
        $this->assertEquals($role, $members->getRole(static::$testDeveloper1->getEmail()));
    }

    /**
     *   To make sure AppGroup has no members.
     *
     * @group online
     */
    public function testAddMember(): void
    {
        $this->getAppGroupMembershipController()->setMembers(new AppGroupMembership([static::$testDeveloper1->getEmail() => '']));
        // Add a new member.
        $this->getAppGroupMembershipController()->setMembers(new AppGroupMembership([static::$testDeveloper2->getEmail() => '']));
        $members = $this->getAppGroupMembershipController()->getMembers();
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

    protected function getAppGroupMembershipController(): AppGroupMembersControllerInterface
    {
        return new AppGroupMembersController(static::$testAppGroup->id(), static::defaultTestOrganization(static::defaultAPIClient()), static::defaultAPIClient());
    }
}
