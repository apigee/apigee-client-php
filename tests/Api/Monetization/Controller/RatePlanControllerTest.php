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
use Apigee\Edge\Api\Monetization\Entity\CompanyRatePlanInterface;
use Apigee\Edge\Api\Monetization\Entity\DeveloperCategoryRatePlanInterface;
use Apigee\Edge\Api\Monetization\Entity\DeveloperRatePlanInterface;
use Apigee\Edge\Api\Monetization\Entity\RatePlanRevisionInterface;
use Apigee\Edge\Api\Monetization\Entity\StandardRatePlanInterface;
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
        $ids = [
            // Standard rate plan.
            'standard' => [StandardRatePlanInterface::class],
            // Standard rate plan revision.
            'standard-rev' => [StandardRatePlanInterface::class, RatePlanRevisionInterface::class],
            // Developer rate plan.
            'developer' => [DeveloperRatePlanInterface::class],
            // Developer rate plan revision.
            'developer-rev' => [DeveloperRatePlanInterface::class, RatePlanRevisionInterface::class],
            // Company (developer) rate plan.
            'company' => [CompanyRatePlanInterface::class],
            // Company (developer) rate plan revision.
            'company-rev' => [CompanyRatePlanInterface::class, RatePlanRevisionInterface::class],
            // Developer category specific rate plan.
            'developer_category' => [DeveloperCategoryRatePlanInterface::class],
            // Developer category specific rate plan revision.
            'developer_category-rev' => [DeveloperCategoryRatePlanInterface::class, RatePlanRevisionInterface::class],
            // Rate plan with revenue share and rate card.
            // We do not expect any class here, because it is not a special
            // type of rate plans.
            'revshare_ratecard' => [],
        ];

        foreach ($ids as $id => $expectedClasses) {
            $entity = $this->loadTestEntity($id);
            foreach ($expectedClasses as $expectedClass) {
                $this->assertInstanceOf($expectedClass, $entity, $id);
            }
            $this->validateLoadedEntity($entity);
        }
    }

    protected function getTestEntityId(): string
    {
        return 'standard';
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
