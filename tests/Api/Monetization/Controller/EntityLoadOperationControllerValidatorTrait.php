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

namespace Apigee\Edge\Tests\Api\Monetization\Controller;

use Apigee\Edge\Api\Monetization\Entity\EntityInterface;
use Apigee\Edge\Tests\Test\Controller\ClientAwareTestTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerAwareTrait;

trait EntityLoadOperationControllerValidatorTrait
{
    use EntityControllerAwareTrait;
    use ClientAwareTestTrait;
    use EntitySerializerAwareValidatorTrait;
    use TestEntityIdAwareControllerValidatorTrait;

    public function testLoad(?string $entityId = null): void
    {
        $entity = $this->loadTestEntity($entityId);
        $this->validateLoadedEntity($entity);
    }

    /**
     * Validates the loaded entity object.
     *
     * @param \Apigee\Edge\Api\Monetization\Entity\EntityInterface $entity
     */
    protected function validateLoadedEntity(EntityInterface $entity): void
    {
        $input = json_decode((string) $this->getClient()->getJournal()->getLastResponse()->getBody());
        /** @var \Apigee\Edge\Tests\Api\Monetization\EntitySerializer\EntitySerializerValidatorInterface $validator */
        $validator = $this->getEntitySerializerValidator();
        $validator->validate($input, $entity);
    }

    /**
     * Loads the test entity for testLoad() which runs validation on it.
     *
     * @param null|string $entityId
     *   Id of the entity to load, falls back to the default test entity id.
     *
     * @return \Apigee\Edge\Api\Monetization\Entity\EntityInterface
     */
    protected function loadTestEntity(?string $entityId = null): EntityInterface
    {
        $entityId = $entityId ?? $this->getTestEntityId();
        /** @var \Apigee\Edge\Api\Monetization\Controller\EntityLoadOperationControllerInterface $controller */
        $controller = $this->getEntityController();

        return $controller->load($entityId);
    }
}
