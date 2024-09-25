<?php

/*
 * Copyright 2021 Google LLC
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

namespace Apigee\Edge\Tests\Api\ApigeeX\EntitySerializer\PropertyValidator;

use Apigee\Edge\Api\ApigeeX\Entity\Property\ApiProductPropertyInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\PropertyValidatorsAwareValidatorInterface;
use Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\PropertyValidatorsAwareValidatorTrait;
use Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\RemoveIfPropertyValidPropertyValidatorInterface;
use Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\SerializerAwarePropertyValidatorInterface;
use Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\SerializerAwarePropertyValidatorTrait;
use PHPUnit\Framework\Assert;
use stdClass;

class ApiProductsPropertyValidator implements RemoveIfPropertyValidPropertyValidatorInterface, SerializerAwarePropertyValidatorInterface, PropertyValidatorsAwareValidatorInterface
{
    use SerializerAwarePropertyValidatorTrait;
    use PropertyValidatorsAwareValidatorTrait;

    /**
     * {@inheritdoc}
     */
    public function validate(stdClass $input, stdClass $output, EntityInterface $entity): void
    {
        if (!$entity instanceof ApiProductPropertyInterface) {
            return;
        }

        // Ensures the serializer could parse all data from the input and
        // every data got parsed properly.
        $i = 0;
        foreach ($entity->getApiProduct() as $product) {
            $json = json_decode($this->entitySerializer->serialize($product, 'json'));
            $this->propertyValidatorsValidate($input->{static::validatedProperty()}[$i], $json, $product);
            ++$i;
        }

        // Ensures the output which is sent to Apigee Edge is correct.
        $expected = [];
        foreach ($entity->getApiProduct() as $product) {
            $expected[] = (object) ['id' => $product->id()];
        }
        Assert::assertEquals($output->{static::validatedProperty()}, $expected);
    }

    /**
     * {@inheritdoc}
     */
    public static function validatedProperty(): string
    {
        return 'product';
    }
}
