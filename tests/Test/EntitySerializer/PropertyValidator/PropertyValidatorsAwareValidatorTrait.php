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

namespace Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator;

use Apigee\Edge\Entity\EntityInterface;
use Exception;
use PHPUnit\Framework\Assert;
use stdClass;

/**
 * Trait PropertyValidatorsAwareValidatorTrait.
 *
 * @see PropertyValidatorsAwareValidatorInterface
 */
trait PropertyValidatorsAwareValidatorTrait
{
    /** @var \Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\PropertyValidatorInterface[] */
    protected $propertyValidators = [];

    /**
     * @param \Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\PropertyValidatorInterface[] $propertyValidators
     */
    public function setPropertyValidators(array $propertyValidators): void
    {
        $this->propertyValidators = $propertyValidators;
    }

    /**
     * Validates properties in input & output with available validators.
     *
     * @param stdClass $input
     *   JSON decoded raw API response as object.
     * @param stdClass $output
     *   API request payload generated by an entity serializer.
     * @param EntityInterface $entity
     *   Entity object in which the output got generated.
     *
     * @throws Exception
     *   By Assert, if validation fails.
     */
    private function propertyValidatorsValidate(stdClass $input, stdClass $output, EntityInterface $entity): void
    {
        foreach ($this->propertyValidators as $validator) {
            $validator->validate(clone $input, clone $output, $entity);
        }
        foreach ($this->propertyValidators as $validator) {
            if ($validator instanceof RemoveIfPropertyValidPropertyValidatorInterface) {
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
