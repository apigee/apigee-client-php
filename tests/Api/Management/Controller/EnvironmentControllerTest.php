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

use Apigee\Edge\Api\Management\Controller\EnvironmentController;
use Apigee\Edge\Api\Management\Entity\Environment;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Test\Controller\EntityCrudOperationsControllerValidator;
use Apigee\Edge\Tests\Test\Controller\NoPaginationEntityIdListingControllerValidatorTrait;
use Apigee\Edge\Tests\Test\Controller\OrganizationAwareEntityControllerValidatorTrait;

/**
 * Class EnvironmentControllerTest.
 *
 * @group controller
 *
 * TODO
 * Finish CRUD tests.
 */
class EnvironmentControllerTest extends EntityCrudOperationsControllerValidator
{
    use OrganizationAwareEntityControllerValidatorTrait;
    use NoPaginationEntityIdListingControllerValidatorTrait {
        testGetEntityIds as private traitTestGetEntityIds;
    }

    private const SKIP_REASON = 'Only available on Apigee Edge on-prem installations. TODO: finish test for this later.';

    /**
     * @inheritdoc
     */
    public static function sampleDataForEntityCreate(): EntityInterface
    {
        return new Environment();
    }

    /**
     * @inheritdoc
     */
    public static function sampleDataForEntityUpdate(): EntityInterface
    {
        return new Environment();
    }

    /**
     * testCreate() is skipped and we do want this test to run.
     *
     * @param string $entityId
     *
     * @throws \ReflectionException
     *
     * @return string
     */
    public function testLoad(string $entityId = 'test')
    {
        $entity = $this->getEntityController()->load($entityId);
        // Validate properties that values are either auto generated or we do not know in the current context.
        $this->assertEntityHasAllPropertiesSet($entity);
        $this->assertArraySubset(
            array_filter(static::$objectNormalizer->normalize(static::expectedAfterEntityCreate())),
            static::$objectNormalizer->normalize($entity)
        );

        return $entityId;
    }

    /**
     * testCreate() is skipped and we do want this test to run.
     */
    public function testGetEntityIds(): void
    {
        $this->traitTestGetEntityIds();
    }

    public function testCreate(): void
    {
        $this->markTestSkipped(self::SKIP_REASON);
    }

    public function testUpdate(string $entityId = 'test'): void
    {
        $this->markTestSkipped(self::SKIP_REASON);
    }

    public function testDelete(): void
    {
        $this->markTestSkipped(self::SKIP_REASON);
    }

    /**
     * @inheritdoc
     */
    protected static function getEntityController(): EntityControllerInterface
    {
        static $controller;
        if (!$controller) {
            $controller = new EnvironmentController(static::getOrganization(static::$client), static::$client);
        }

        return $controller;
    }
}
