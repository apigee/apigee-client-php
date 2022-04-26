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

use Apigee\Edge\ClientInterface;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerAwareTestTrait;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerValidator;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerValidatorAwareTrait;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerValidatorInterface;
use Apigee\Edge\Tests\Test\Utility\EntityStorage;

abstract class EntityControllerTestBase extends ControllerTestBase
{
    use EntitySerializerAwareTestTrait;
    use EntitySerializerValidatorAwareTrait;

    protected static $instance;
    protected static $validator;

    /**
     * {@inheritdoc}
     */
    public static function tearDownAfterClass(): void
    {
        EntityStorage::getInstance()->purgeCreatedEntities();
        parent::tearDownAfterClass();
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown(): void
    {
        // Clearing the static variables.
        static::$instance = null;
        static::$validator = null;
    }

    abstract protected static function entityController(ClientInterface $client = null): EntityControllerTesterInterface;

    /**
     * {@inheritdoc}
     */
    protected function entitySerializer(): EntitySerializerInterface
    {
        if (null === static::$instance) {
            // TODO Find a better way to expose the entity serializer used by
            // a controller.
            $ro = new \ReflectionObject(static::entityController());
            $property = $ro->getProperty('decorated');
            $property->setAccessible(true);
            $ro = new \ReflectionObject($property->getValue(static::entityController()));
            $rm = $ro->getMethod('getEntitySerializer');
            $rm->setAccessible(true);

            static::$instance = $rm->invoke($property->getValue(static::entityController()));
        }

        return static::$instance;
    }

    /**
     * {@inheritdoc}
     */
    protected function entitySerializerValidator(): EntitySerializerValidatorInterface
    {
        if (null === static::$validator) {
            static::$validator = new EntitySerializerValidator($this->entitySerializer());
        }

        return static::$validator;
    }

}
