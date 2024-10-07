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

use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Api\Management\Controller\ApiProductControllerAwareTestTrait;
use Apigee\Edge\Tests\Api\Management\Controller\NonPaginatedEntityListingControllerTestTrait;
use Apigee\Edge\Tests\Api\Management\Entity\ApiProductTestEntityProviderTrait;
use Apigee\Edge\Tests\Api\Management\EntitySerializer\AppSerializerValidator;
use Apigee\Edge\Tests\Test\Controller\DefaultAPIClientAwareTrait;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityCreateOperationTestControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityDeleteOperationControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityLoadOperationControllerTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityUpdateOperationControllerTestTrait;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerValidatorInterface;
use Apigee\Edge\Tests\Test\TestClientFactory;
use PHPUnit\Framework\Assert;
use stdClass;

/**
 * Base class for developer- and company app controller tests.
 */
abstract class AppControllerTestBase extends EntityControllerTestBase
{
    use ApiProductControllerAwareTestTrait;
    use ApiProductTestEntityProviderTrait;
    use DefaultAPIClientAwareTrait;
    // The order of these trait matters. Check @depends in test methods.
    use EntityCreateOperationControllerTestTrait;
    use EntityLoadOperationControllerTestTrait;
    use EntityUpdateOperationControllerTestTrait;
    use EntityDeleteOperationControllerTestTrait;
    use AttributesAwareEntityControllerTestTrait;
    use NonPaginatedEntityListingControllerTestTrait;
    use PaginatedEntityListingControllerTestTraitBase;

    /** @var \Apigee\Edge\Api\ApigeeX\Entity\ApiProductInterface */
    protected static $testApiProduct;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$testApiProduct = static::getNewApiProduct();
        static::apiProductController(static::defaultAPIClient())->create(static::$testApiProduct);
    }

    /**
     * It is easier to test it here instead in the DeveloperControllerTest
     * or CompanyControllerTest.
     */
    public function testHasApp(): void
    {
        if (TestClientFactory::isOfflineClient(static::defaultAPIClient())) {
            Assert::markTestSkipped(__FUNCTION__ . ' can be executed only with an online API test client.');
        }
        /** @var \Apigee\Edge\Api\Management\Controller\DeveloperAppControllerInterface $controller */
        $controller = static::entityController();
        /** @var \Apigee\Edge\Api\Management\Entity\DeveloperAppInterface $entity */
        $entity = static::getNewEntity();
        $controller->create($entity);
        /** @var \Apigee\Edge\Api\Management\Entity\DeveloperInterface|\Apigee\Edge\Api\ApigeeX\Entity\AppGroupInterface $appOwner */
        $appOwner = static::reloadAppOwner();
        $this->assertTrue($appOwner->hasApp($entity->getName()));
        $controller->delete($entity->id());
        $appOwner = static::reloadAppOwner();
        $this->assertFalse($appOwner->hasApp($entity->getName()));
    }

    /**
     * {@inheritdoc}
     */
    protected static function entityCreateOperationTestController(): EntityCreateOperationTestControllerTesterInterface
    {
        return new EntityCreateOperationControllerTester(static::entityController());
    }

    /**
     * Reloads the developer from Apigee Edge.
     *
     * @return \Apigee\Edge\Api\Management\Entity\DeveloperInterface|\Apigee\Edge\Api\ApigeeX\Entity\AppGroupInterface
     */
    abstract protected function reloadAppOwner();

    /**
     * {@inheritdoc}
     */
    protected function alterArraysBeforeCompareSentAndReceivedPayloadsInCreate(array &$sentEntityAsArray, array $responseEntityAsArray): void
    {
        // This is not returned in the POST/PUT API call responses only in GET.
        unset($sentEntityAsArray['appFamily']);
        unset($sentEntityAsArray['apiProducts']);
    }

    /**
     * {@inheritdoc}
     */
    protected function alterObjectsBeforeCompareResponseAndCreatedEntity(stdClass &$responseObject, EntityInterface $created): void
    {
        /* @var \Apigee\Edge\Api\Management\Entity\DeveloperAppInterface $created */
        $responseObject->apiProducts = $created->getApiProducts();
    }

    /**
     * {@inheritdoc}
     */
    protected function alterArraysBeforeCompareSentAndReceivedPayloadsInUpdate(array &$sentEntityAsArray, array $responseEntityAsArray): void
    {
        $this->alterArraysBeforeCompareSentAndReceivedPayloadsInCreate($sentEntityAsArray, $responseEntityAsArray);
        $sentEntityAsArray['credentials'][0]['issuedAt'] = $responseEntityAsArray['credentials'][0]['issuedAt'];
    }

    /**
     * {@inheritdoc}
     */
    protected function alterObjectsBeforeCompareResponseAndUpdateEntity(stdClass &$responseObject, EntityInterface $created): void
    {
        $this->alterObjectsBeforeCompareResponseAndCreatedEntity($responseObject, $created);
    }

    /**
     * {@inheritdoc}
     */
    protected function entitySerializerValidator(): EntitySerializerValidatorInterface
    {
        static $validator;
        if (null === $validator) {
            $validator = new AppSerializerValidator($this->entitySerializer());
        }

        return $validator;
    }
}
