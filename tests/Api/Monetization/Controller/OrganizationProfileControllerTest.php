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
use Apigee\Edge\Api\Monetization\Entity\EntityInterface;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Tests\Test\Controller\OrganizationAwareEntityControllerValidatorTrait;

class OrganizationProfileControllerTest extends EntityControllerValidator
{
    use OrganizationAwareEntityControllerValidatorTrait;
    use EntityUpdateOperationControllerValidatorTrait;

    public function testLoad(): void
    {
        $entity = $this->getEntityController()->load();
        $input = json_decode((string) static::$client->getJournal()->getLastResponse()->getBody());
        $this->getEntitySerializerValidator()->validate($input, $entity);
        $entity = (new OrganizationProfileController('phpunit-minimal', static::$client))->load();
        $input = json_decode((string) static::$client->getJournal()->getLastResponse()->getBody());
        $this->getEntitySerializerValidator()->validate($input, $entity);
    }

    /**
     * @inheritdoc
     */
    protected static function getEntityController(): EntityControllerInterface
    {
        static $controller;
        if (null === $controller) {
            $controller = new OrganizationProfileController(static::getOrganization(static::$client), static::$client);
        }

        return $controller;
    }

    protected function getEntityForTestUpdate(): EntityInterface
    {
        return $this->getEntityController()->load();
    }
}
