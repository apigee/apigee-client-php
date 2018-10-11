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

use Apigee\Edge\Api\Monetization\Controller\RatePlanController;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\EntitySerializerValidatorInterface;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\RatePlanSerializerValidator;

class RatePlanControllerTest extends OrganizationAwareEntityControllerValidator
{
    use EntityLoadOperationControllerValidatorTrait {
        testLoad as private traitTestLoad;
    }
    use TimezoneConversionValidatorTrait;

    /**
     * @inheritdoc
     */
    public function testLoad(): void
    {
        // Standard rate plan.
        $entity = $this->loadTestEntity();
        $this->validateLoadedEntity($entity);
        // Developer rate plan.
        $entity = $this->loadTestEntity('developer');
        $this->validateLoadedEntity($entity);
        // Company (developer) rate plan.
        $entity = $this->loadTestEntity('company');
        $this->validateLoadedEntity($entity);
        // Rate plan with revenue share and rate card.
        $entity = $this->loadTestEntity('revshare_ratecard');
        $this->validateLoadedEntity($entity);
        // Developer category specific rate plan.
        $entity = $this->loadTestEntity('developer_category');
        $this->validateLoadedEntity($entity);
    }

    /**
     * @inheritdoc
     */
    protected static function getEntitySerializerValidator(): EntitySerializerValidatorInterface
    {
        static $validator;
        if (null === $validator) {
            $validator = new RatePlanSerializerValidator(static::getEntitySerializer());
        }

        return $validator;
    }

    /**
     * @inheritdoc
     */
    protected static function getEntityController(): EntityControllerInterface
    {
        static $controller;
        if (null === $controller) {
            $controller = new RatePlanController('phpunit', static::getOrganization(static::$client), static::$client);
        }

        return $controller;
    }
}
