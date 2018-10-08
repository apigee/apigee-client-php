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

class RatePlanControllerValidator extends OrganizationAwareEntityControllerValidator
{
    use EntityLoadOperationControllerValidatorTrait {
        testLoad as private traitTestLoad;
    }

    /**
     * @inheritdoc
     */
    public function testLoad(): \Apigee\Edge\Api\Monetization\Entity\EntityInterface
    {
        // TODO Validate timezone conversion.
        date_default_timezone_set('Australia/Eucla');
        // Validate standard rate plan.
        $entity = $this->traitTestLoad();

        // Add tests for company_rate_plan, developer_rate_plan, developer_category_rate_plan
        // as well.
        /* @var \Apigee\Edge\Api\Monetization\Controller\EntityLoadOperationControllerInterface $controller */
//        $controller = $this->getEntityController();
//        $entity = $controller->load($this->getEntityId());
        return $entity;
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
