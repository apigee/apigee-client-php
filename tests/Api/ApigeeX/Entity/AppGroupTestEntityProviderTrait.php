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

namespace Apigee\Edge\Tests\Api\ApigeeX\Entity;

use Apigee\Edge\Api\ApigeeX\Entity\AppGroup;
use Apigee\Edge\Api\ApigeeX\Entity\AppGroupInterface;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Tests\Test\Utility\RandomGeneratorAwareTrait;

trait AppGroupTestEntityProviderTrait
{
    use RandomGeneratorAwareTrait;

    protected static function getNewAppGroup(bool $randomData = true): AppGroupInterface
    {
        $entity = new AppGroup([
            'name' => $randomData ? static::randomGenerator()->machineName() : 'phpunit',
            'displayName' => $randomData ? static::randomGenerator()->displayName() : 'A PHPUnit appgroup',
            'attributes' => new AttributesProperty(['foo' => 'bar']),
        ]);

        return $entity;
    }

    protected static function getUpdatedAppGroup(AppGroupInterface $existing, bool $randomData = true): AppGroupInterface
    {
        $updated = clone $existing;
        $updated->setDisplayName($randomData ? static::randomGenerator()->displayName() : '(Edited) A PHPUnit appgroup');
        $updated->setAttributes(new AttributesProperty(['foo' => 'foo', 'bar' => 'baz']));

        return $updated;
    }
}
