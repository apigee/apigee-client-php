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
use Apigee\Edge\Tests\Test\Controller\DefaultAPIClientAwareTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerAwareTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityUpdateOperationControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityUpdateOperationControllerTesterInterface;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerAwareTestTrait;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerValidatorAwareTrait;
use DMS\PHPUnitExtensions\ArraySubset\Assert;

/**
 * Validates controllers that support entity update operations.
 *
 * @see \Apigee\Edge\Controller\EntityUpdateOperationControllerInterface
 */
trait EntityUpdateOperationControllerTestTrait
{
    use EntityControllerAwareTestTrait;
    use EntitySerializerAwareTestTrait;
    use EntitySerializerValidatorAwareTrait;
    use DefaultAPIClientAwareTrait;

    /**
     * @depends testLoad
     *
     * @param \Apigee\Edge\Entity\EntityInterface $existing
     */
    public function testUpdate(EntityInterface $existing): EntityInterface
    {
        $entity = $this->entityForUpdateTest($existing);
        $original = clone $entity;
        static::controllerForEntityUpdate()->update($entity);
        $this->validateUpdatedEntity($entity, $original);

        return $entity;
    }

    protected function validateUpdatedEntity(EntityInterface $updated, EntityInterface $original): void
    {
        // Validate we get back the same values as we sent.
        $sentAsArray = json_decode((string) static::defaultAPIClient()->getJournal()->getLastRequest()->getBody(), true);
        $responsePayload = (string) static::defaultAPIClient()->getJournal()->getLastResponse()->getBody();
        // We do not store milliseconds so these values should not be equal
        // anyway.
        unset($sentAsArray['createdAt']);
        unset($sentAsArray['lastModifiedAt']);
        $responseAsArray = json_decode($responsePayload, true);
        $this->alterArraysBeforeCompareSentAndReceivedPayloadsInUpdate($sentAsArray, $responseAsArray);
        Assert::assertArraySubset($sentAsArray, $responseAsArray);

        // Validate that the PHP Client could parse all information from the
        // API response.
        $responseObject = json_decode($responsePayload);
        $this->alterObjectsBeforeCompareResponseAndUpdateEntity($responseObject, $updated);
        $this->entitySerializerValidator()->validate($responseObject, $updated);
    }

    abstract protected function entityForUpdateTest(EntityInterface $existing): EntityInterface;

    protected function alterArraysBeforeCompareSentAndReceivedPayloadsInUpdate(array &$sentEntityAsArray, array $responseEntityAsArray): void
    {
    }

    protected function alterObjectsBeforeCompareResponseAndUpdateEntity(\stdClass &$responseObject, EntityInterface $created): void
    {
    }

    /**
     * Controller for entity update operation testing.
     *
     * @return \Apigee\Edge\Tests\Test\Controller\EntityUpdateOperationControllerTesterInterface
     */
    protected static function controllerForEntityUpdate(): EntityUpdateOperationControllerTesterInterface
    {
        /** @var \Apigee\Edge\Controller\EntityUpdateOperationControllerInterface $controller */
        $controller = static::entityController();

        return new EntityUpdateOperationControllerTester($controller);
    }
}
