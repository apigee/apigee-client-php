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

namespace Apigee\Edge\Tests\Api\Monetization\EntitySerializer\PropertyValidator;

use Apigee\Edge\Api\Monetization\Entity\EntityInterface;
use PHPUnit\Framework\Assert;

trait PropertyValidatorTrait
{
    /** @var \Apigee\Edge\Tests\Api\Monetization\EntitySerializer\PropertyValidator\PropertyValidatorInterface[] */
    protected $propertyValidators = [];

    private function propertyValidatorsValidate(\stdClass $input, \stdClass $output, EntityInterface $entity): void
    {
        foreach ($this->propertyValidators as $validator) {
            $validator->validate(clone $input, clone $output, $entity);
        }
        foreach ($this->propertyValidators as $validator) {
            if ($validator instanceof EntityReferencePropertyValidatorInterface) {
                if (property_exists($input, $validator->validatedProperty())) {
                    unset($input->{$validator->validatedProperty()});
                }
                if (property_exists($output, $validator->validatedProperty())) {
                    unset($output->{$validator->validatedProperty()});
                }
            }
        }
        Assert::assertEquals($input, $output);
    }
}
