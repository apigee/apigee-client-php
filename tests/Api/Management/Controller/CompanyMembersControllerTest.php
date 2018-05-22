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
use Apigee\Edge\Exception\ApiException;
use Apigee\Edge\Tests\Test\Controller\AbstractControllerValidator;
use Apigee\Edge\Tests\Test\Controller\OrganizationAwareEntityControllerValidatorTrait;
use Apigee\Edge\Tests\Test\TestClientFactory;

class CompanyMembersControllerTest extends AbstractControllerValidator
{
    use CompanyAwareControllerTestTrait;
    use DeveloperAwareControllerTestTrait;
    use OrganizationAwareEntityControllerValidatorTrait;

    public static function setUpBeforeClass(): void
    {
        try {
            parent::setUpBeforeClass();
            static::setupCompany();
            static::setupDeveloper();
        } catch (ApiException $e) {
            // Ensure that created test data always gets removed after an API call fails here.
            // (By default tearDownAfterClass() is not called if (any) exception occurred here.)
            static::tearDownAfterClass();
            throw $e;
        }
    }

    public static function tearDownAfterClass(): void
    {
        static::tearDownDeveloper();
        static::tearDownCompany();
        parent::tearDownAfterClass();
    }

    public function testCreateMembership(): void
    {
        $role = 'simple member';
        $members = $this->getController()->setMembers(new CompanyMembership([DeveloperControllerTest::sampleDataForEntityCreate()->getEmail() => $role]));
        $this->assertTrue($members->isMember(DeveloperControllerTest::sampleDataForEntityCreate()->getEmail()));
        $this->assertEquals($role, $members->getRole(DeveloperControllerTest::sampleDataForEntityCreate()->getEmail()));
    }

    /**
     * @depends testCreateMembership
     */
    public function testUpdateMembership(): void
    {
        if (TestClientFactory::isMockClient(static::$client)) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }

        $role = 'not a simple member';
        $members = $this->getController()->setMembers(new CompanyMembership([DeveloperControllerTest::sampleDataForEntityCreate()->getEmail() => $role]));
        $this->assertTrue($members->isMember(DeveloperControllerTest::sampleDataForEntityCreate()->getEmail()));
        $this->assertEquals($role, $members->getRole(DeveloperControllerTest::sampleDataForEntityCreate()->getEmail()));
    }

    /**
     * @depends testCreateMembership
     */
    public function testDeleteMembership(): void
    {
        $this->getController()->removeMember(DeveloperControllerTest::sampleDataForEntityCreate()->getEmail());
        $members = $this->getController()->getMembers();
        $this->assertEmpty($members->getMembers());
    }

    public function testDeleteMembershipWithEmptyMemberList(): void
    {
        if (TestClientFactory::isMockClient(static::$client)) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }

        $role = 'simple member';
        $members = $this->getController()->setMembers(new CompanyMembership([DeveloperControllerTest::sampleDataForEntityCreate()->getEmail() => $role]));
        $this->assertTrue($members->isMember(DeveloperControllerTest::sampleDataForEntityCreate()->getEmail()));
        $this->assertEquals($role, $members->getRole(DeveloperControllerTest::sampleDataForEntityCreate()->getEmail()));
        $members = $this->getController()->setMembers(new CompanyMembership());
        $this->assertEmpty($members->getMembers());
    }

    /**
     * Returns a configured StatsController that uses the FileSystemMockClient http client.
     *
     * @return \Apigee\Edge\Api\Management\Controller\CompanyMembersControllerInterface
     */
    private function getController(): CompanyMembersControllerInterface
    {
        static $controller;
        if (!$controller) {
            $controller = new CompanyMembersController(static::$companyName, static::getOrganization(static::$client), static::$client);
        }

        return $controller;
    }
}
