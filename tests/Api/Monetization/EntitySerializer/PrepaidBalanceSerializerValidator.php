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

namespace Apigee\Edge\Tests\Api\Monetization\EntitySerializer;

use Apigee\Edge\Serializer\EntitySerializerInterface;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\PropertyValidator\DeveloperCategoryEntityReferencePropertyValidator;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\PropertyValidator\LegalEntityEntityReferencePropertyValidator;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\PropertyValidator\OrganizationProfileEntityReferencePropertyValidator;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\PropertyValidator\PaymentTransactionPropertyValidator;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\PropertyValidator\SupportedCurrencyEntityReferencePropertyValidator;

class PrepaidBalanceSerializerValidator extends OrganizationAwareEntitySerializerValidator
{
    /**
     * PrepaidBalanceSerializerValidator constructor.
     *
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface $serializer
     * @param array $propertyValidators
     */
    public function __construct(EntitySerializerInterface $serializer = null, array $propertyValidators = [])
    {
        $propertyValidators = array_merge($propertyValidators, [
            new SupportedCurrencyEntityReferencePropertyValidator(),
            new LegalEntityEntityReferencePropertyValidator(),
            new DeveloperCategoryEntityReferencePropertyValidator(),
            new OrganizationProfileEntityReferencePropertyValidator(),
            new PaymentTransactionPropertyValidator(),
        ]);
        parent::__construct($serializer, $propertyValidators);
    }
}
