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

/**
 * Trait PaginatedEntityListingControllerValidatorTrait.
 *
 * @see \Apigee\Edge\Api\Monetization\Controller\PaginatedEntityListingControllerInterface
 */
trait PaginatedEntityListingControllerValidatorTrait
{
    public function testLoadAll(): void
    {
        $entities = $this->getEntityController()->loadAll();
        $this->assertCount(2, $entities);
    }

    public function testLoadSubset(): void
    {
        $entities = $this->getEntityController()->loadSubset(1, 2);
        $this->assertCount(1, $entities);
        $entity = reset($entities);
        $this->assertEquals('phpunit_2', $entity->id());
    }
}
