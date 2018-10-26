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

namespace Apigee\Edge\Tests\Api\Management\Entity;

use Apigee\Edge\Api\Management\Entity\Developer;
use Apigee\Edge\Api\Management\Entity\DeveloperInterface;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Tests\Test\Utility\RandomGeneratorAwareTrait;

trait DeveloperTestEntityProviderTrait
{
    use RandomGeneratorAwareTrait;

    protected static function getNewDeveloper(bool $randomData = true): DeveloperInterface
    {
        $entity = new Developer([
            'email' => $randomData ? static::randomGenerator()->email() : 'phpunit@example.com',
            'firstName' => $randomData ? static::randomGenerator()->displayName() : 'Php',
            'lastName' => $randomData ? static::randomGenerator()->displayName() : 'Unit',
            'userName' => $randomData ? static::randomGenerator()->machineName() : 'phpunit',
            'attributes' => new AttributesProperty(['foo' => 'bar']),
        ]);

        return $entity;
    }

    protected static function getUpdatedDeveloper(DeveloperInterface $existing, bool $randomData = true): DeveloperInterface
    {
        $updated = clone $existing;
        $updated->setEmail($randomData ? static::randomGenerator()->email() : 'phpunit-edited@example.com');
        $updated->setAttributes(new AttributesProperty(['foo' => 'foo', 'bar' => 'baz']));

        return $updated;
    }
}
