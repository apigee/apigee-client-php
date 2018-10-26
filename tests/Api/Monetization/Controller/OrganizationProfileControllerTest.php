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

use Apigee\Edge\Api\Monetization\Controller\OrganizationProfileController;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Test\Controller\DefaultTestOrganizationAwareTrait;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTester;
use Apigee\Edge\Tests\Test\Controller\EntityControllerTesterInterface;
use Apigee\Edge\Tests\Test\Controller\EntityUpdateOperationControllerTestTrait;

/**
 * Class OrganizationProfileControllerTest.
 *
 * @group controller
 * @group monetization
 */
class OrganizationProfileControllerTest extends EntityControllerTestBase
{
    use DefaultTestOrganizationAwareTrait;
    use EntityLoadOperationControllerTestTrait {
        testLoad as private traitTestLoad;
    }
    use EntityUpdateOperationControllerTestTrait;

    /**
     * @inheritDoc
     */
    public function testLoad($created = null): EntityInterface
    {
        $this->traitTestLoad('phpunit-minimal');

        return $this->traitTestLoad('phpunit');
    }

    /**
     * @inheritdoc
     */
    protected static function entityController(): EntityControllerTesterInterface
    {
        return new EntityControllerTester(new OrganizationProfileController(static::defaultTestOrganization(static::defaultAPIClient()), static::defaultAPIClient()));
    }

    /**
     * @inheritdoc
     */
    protected function entityForUpdateTest(EntityInterface $existing): EntityInterface
    {
        /** @var \Apigee\Edge\Api\Monetization\Entity\OrganizationProfileInterface $updated */
        $updated = clone $existing;
        $updated->setTransactionRelayURL('http://example.com');
        $updated->setLogoUrl('http://foo.example.com');

        return $updated;
    }
}
