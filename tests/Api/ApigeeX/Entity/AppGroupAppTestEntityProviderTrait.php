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

use Apigee\Edge\Api\ApigeeX\Entity\AppGroupApp;
use Apigee\Edge\Api\ApigeeX\Entity\AppGroupAppInterface;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Tests\Test\Utility\RandomGeneratorAwareTrait;

trait AppGroupAppTestEntityProviderTrait
{
    use RandomGeneratorAwareTrait;

    protected static function getNewAppGroupApp(array $apiProducts = [], bool $randomData = true): AppGroupAppInterface
    {
        $entity = new AppGroupApp([
            'name' => $randomData ? static::randomGenerator()->machineName() : 'phpunit_test_app',
            'apiProducts' => $apiProducts,
            'callbackUrl' => 'http://example.com',
        ]);
        $entity->setDisplayName($randomData ? static::randomGenerator()->displayName() : 'PHP Unit: Test app');
        $entity->setDescription($randomData ? static::randomGenerator()->text() : 'This is a test app created by PHP Unit.');
        // We have to use this trick to ensure the order of the attributes is
        // the same as Apigee Edge returns.
        $entity->setAttributes(new AttributesProperty(['DisplayName' => $entity->getDisplayName(), 'Notes' => $entity->getDescription(), 'foo' => 'bar']));

        return $entity;
    }

    protected static function getUpdatedAppGroupApp(AppGroupAppInterface $existing, bool $randomData = true): AppGroupAppInterface
    {
        $updated = clone $existing;
        $updated->setCallbackUrl('http://foo.example.com');
        $updated->setAttributes(new AttributesProperty(['foo' => 'foo', 'bar' => 'baz']));
        $updated->setDisplayName($randomData ? static::randomGenerator()->displayName() : '(Edited) PHP Unit: Test app');
        $updated->setDescription($randomData ? static::randomGenerator()->text() : '(Edited) This is a test app created by PHP Unit.');

        return $updated;
    }
}
