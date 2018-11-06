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

use Apigee\Edge\Api\Monetization\Serializer\EntitySerializer;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\EntitySerializerValidator;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\EntitySerializerValidatorInterface;
use Apigee\Edge\Tests\Test\Controller\EntityControllerAwareTrait;

abstract class EntityControllerValidator extends AbstractControllerValidator
{
    use EntityControllerAwareTrait;

    /** @var \Symfony\Component\Serializer\Serializer */
    protected static $entitySerializer;

    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$entitySerializer = new EntitySerializer();
    }

    protected static function getEntitySerializer(): EntitySerializerInterface
    {
        $ro = new \ReflectionObject(static::getEntityController());
        $rm = $ro->getMethod('getEntitySerializer');
        $rm->setAccessible(true);

        return $rm->invoke(static::getEntityController());
    }

    protected static function getEntitySerializerValidator(): EntitySerializerValidatorInterface
    {
        static $validator;
        if (null === $validator) {
            $validator = new EntitySerializerValidator(static::getEntitySerializer());
        }

        return $validator;
    }
}
