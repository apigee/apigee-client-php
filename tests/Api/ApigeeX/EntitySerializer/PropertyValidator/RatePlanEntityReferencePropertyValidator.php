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

use Apigee\Edge\Api\ApigeeX\Entity\AcceptedRatePlanInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Api\ApigeeX\EntitySerializer\RatePlanSerializerValidator;
use Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\RemoveIfPropertyValidPropertyValidatorInterface;
use Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\SerializerAwarePropertyValidatorTrait;
use PHPUnit\Framework\Assert;

class RatePlanEntityReferencePropertyValidator implements RemoveIfPropertyValidPropertyValidatorInterface, \Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\SerializerAwarePropertyValidatorInterface
{
    use SerializerAwarePropertyValidatorTrait {
        setEntitySerializer as private traitSetEntitySerializer;
    }

    private $ratePlanEntitySerializerValidator;

    /**
     * RatePlanEntityReferencePropertyValidator constructor.
     */
    public function __construct()
    {
        $this->ratePlanEntitySerializerValidator = new RatePlanSerializerValidator();
    }

    /**
     * {@inheritdoc}
     */
    public function validate(\stdClass $input, \stdClass $output, EntityInterface $entity): void
    {
        if (!$entity instanceof AcceptedRatePlanInterface) {
            return;
        }

        Assert::assertEquals($output, $input);

        // Validate the developerSubscriptions object.
        $expected = clone $input;
        $this->ratePlanEntitySerializerValidator->validate($expected, $entity);
    }

    /**
     * {@inheritdoc}
     */
    public static function validatedProperty(): string
    {
        return 'ratePlan';
    }

    /**
     * {@inheritdoc}
     */
    public function setEntitySerializer(\Apigee\Edge\Serializer\EntitySerializerInterface $entitySerializer): void
    {
        $this->traitSetEntitySerializer($entitySerializer);
        $this->ratePlanEntitySerializerValidator = new RatePlanSerializerValidator($entitySerializer);
    }
}
