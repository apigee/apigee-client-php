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

use Apigee\Edge\Api\Monetization\Controller\SupportedCurrencyController;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Tests\Test\Controller\OrganizationAwareEntityControllerValidatorTrait;

class SupportedCurrenciesControllerTest extends OrganizationAwareEntityControllerValidator
{
    use OrganizationAwareEntityControllerValidatorTrait;
    use EntityCreateOperationControllerValidatorTrait;
    use EntityLoadOperationControllerValidatorTrait;
    use EntityUpdateOperationControllerValidatorTrait;
    use EntityDeleteOperationControllerValidatorTrait;

    /**
     * @inheritdoc
     */
    protected static function getEntityController(): EntityControllerInterface
    {
        static $controller;
        if (null === $controller) {
            $controller = new SupportedCurrencyController(static::getOrganization(static::$client), static::$client);
        }

        return $controller;
    }
}
