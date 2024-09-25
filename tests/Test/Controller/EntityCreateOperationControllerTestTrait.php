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

namespace Apigee\Edge\Tests\Test\Controller;

use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Test\Entity\NewEntityProviderTrait;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerAwareTestTrait;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerValidatorAwareTrait;
use DMS\PHPUnitExtensions\ArraySubset\Assert;
use stdClass;

/**
 * Validates controllers that support entity create operations.
 *
 * @see \Apigee\Edge\Controller\EntityCreateOperationControllerInterface
 */
trait EntityCreateOperationControllerTestTrait
{
    use EntityControllerAwareTestTrait;
    use EntitySerializerAwareTestTrait;
    use EntitySerializerValidatorAwareTrait;
    use EntityCreateOperationTestControllerAwareTrait;
    use NewEntityProviderTrait;

    public function testCreate(): EntityInterface
    {
        $entity = $this->entityForCreateTest();
        $original = clone $entity;
        static::controllerForEntityCreate()->create($entity);
        $this->validateCreatedEntity($entity, $original);

        return $entity;
    }

    protected function validateCreatedEntity(EntityInterface $created, EntityInterface $sent): void
    {
        // Validate we get back the same values as we sent.
        $sentAsArray = json_decode((string) static::defaultAPIClient()->getJournal()->getLastRequest()->getBody(), true);
        $responsePayload = (string) static::defaultAPIClient()->getJournal()->getLastResponse()->getBody();
        $responseAsArray = json_decode($responsePayload, true);
        $this->alterArraysBeforeCompareSentAndReceivedPayloadsInCreate($sentAsArray, $responseAsArray);
        Assert::assertArraySubset($sentAsArray, $responseAsArray);

        // Validate that the PHP Client could parse all information from the
        // API response.
        $responseAsObject = json_decode($responsePayload);
        $this->alterObjectsBeforeCompareResponseAndCreatedEntity($responseAsObject, $created);
        $this->entitySerializerValidator()->validate($responseAsObject, $created);
    }

    protected function alterArraysBeforeCompareSentAndReceivedPayloadsInCreate(array &$sentEntityAsArray, array $responseEntityAsArray): void
    {
    }

    protected function alterObjectsBeforeCompareResponseAndCreatedEntity(stdClass &$responseObject, EntityInterface $created): void
    {
    }

    protected function entityForCreateTest(): EntityInterface
    {
        return static::getNewEntity();
    }

    /**
     * Controller for entity create operation testing.
     *
     * @return EntityCreateOperationTestControllerTesterInterface
     */
    protected static function controllerForEntityCreate(): EntityCreateOperationTestControllerTesterInterface
    {
        return static::entityCreateOperationTestController();
    }
}
