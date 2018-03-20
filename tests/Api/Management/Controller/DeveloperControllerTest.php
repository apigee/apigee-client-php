<?php

/**
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

use Apigee\Edge\Api\Management\Controller\DeveloperController;
use Apigee\Edge\Api\Management\Entity\Developer;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Tests\Test\Controller\AttributesAwareEntityControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\CpsLimitEntityControllerValidator;
use Apigee\Edge\Tests\Test\Controller\OrganizationAwareEntityControllerValidatorTrait;
use Apigee\Edge\Tests\Test\Mock\TestClientFactory;

/**
 * Class DeveloperControllerTest.
 *
 *
 * @group controller
 */
class DeveloperControllerTest extends CpsLimitEntityControllerValidator
{
    use AttributesAwareEntityControllerTestTrait;
    use OrganizationAwareEntityControllerValidatorTrait;

    /**
     * @inheritdoc
     */
    public static function sampleDataForEntityCreate(): EntityInterface
    {
        static $entity;
        if (null === $entity) {
            $isMock = TestClientFactory::isMockClient(static::$client);
            $entity = new Developer([
                'email' => $isMock ? 'phpunit@example.com' : static::$random->unique()->safeEmail,
                'firstName' => $isMock ? 'Php' : static::$random->unique()->firstName,
                'lastName' => $isMock ? 'Unit' : static::$random->unique()->lastName,
                'userName' => $isMock ? 'phpunit' : static::$random->unique()->userName,
                'attributes' => new AttributesProperty(['foo' => 'bar']),
            ]);
        }

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public static function sampleDataForEntityUpdate(): EntityInterface
    {
        static $entity;
        if (null === $entity) {
            $isMock = TestClientFactory::isMockClient(static::$client);
            $entity = new Developer([
                'email' => $isMock ? 'phpunit-edited@example.com' : static::$random->unique()->safeEmail,
                'firstName' => $isMock ? 'Php' : static::$random->unique()->firstName,
                'lastName' => $isMock ? 'Unit' : static::$random->unique()->lastName,
                'userName' => $isMock ? 'phpunit' : static::$random->unique()->userName,
                'attributes' => new AttributesProperty(['foo' => 'foo', 'bar' => 'baz']),
            ]);
        }

        return $entity;
    }

    /**
     * @group online
     * @expectedException \Apigee\Edge\Exception\ClientErrorException
     */
    public function testCreateWithIncorrectData(): void
    {
        if (TestClientFactory::isMockClient(static::$client)) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }
        $entity = new Developer(['email' => 'developer-create-exception@example.com']);
        static::getEntityController()->create($entity);
    }

    /**
     * We have to override this otherwise dependents of this function are being skipped.
     * Also, "@inheritdoc" is not going to work in case of "@depends" annotations so those must be repeated.
     *
     * @inheritdoc
     */
    public function testCreate()
    {
        return parent::testCreate();
    }

    /**
     * We have to override this otherwise dependents of this function are being skipped.
     * Also, "@inheritdoc" is not going to work in case of "@depends" annotations so those must be repeated.
     *
     * @depends testCreate
     */
    public function testLoad(string $entityId)
    {
        return parent::testLoad($entityId);
    }

    /**
     * @depends testLoad
     *
     * @param string $entityId
     */
    public function testStatusChange(string $entityId): void
    {
        if (TestClientFactory::isMockClient(static::$client)) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }
        $entity = static::getEntityController()->load($entityId);
        static::getEntityController()->setStatus($entity->id(), Developer::STATUS_INACTIVE);
        /** @var \Apigee\Edge\Api\Management\Entity\DeveloperInterface $entity */
        $entity = static::getEntityController()->load($entity->id());
        $this->assertEquals($entity->getStatus(), Developer::STATUS_INACTIVE);
        static::getEntityController()->setStatus($entity->id(), Developer::STATUS_ACTIVE);
        /** @var \Apigee\Edge\Api\Management\Entity\DeveloperInterface $entity */
        $entity = static::getEntityController()->load($entity->id());
        $this->assertEquals($entity->getStatus(), Developer::STATUS_ACTIVE);
    }

    /**
     * @inheritdoc
     */
    public function cpsLimitTestIdFieldProvider()
    {
        // This override makes easier the offline testing.
        return [['email']];
    }

    /**
     * @inheritdoc
     */
    protected static function getEntityController(): EntityControllerInterface
    {
        static $controller;
        if (!$controller) {
            $controller = new DeveloperController(static::getOrganization(static::$client), static::$client);
        }

        return $controller;
    }

    /**
     * @inheritdoc
     */
    protected static function expectedAfterEntityCreate(): EntityInterface
    {
        /** @var Developer $entity */
        $entity = clone static::sampleDataForEntityCreate();
        // We can be sure one another thing, the status of the created developer is active by default.
        $entity->setStatus(Developer::STATUS_ACTIVE);

        return $entity;
    }
}
