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

use Apigee\Edge\Api\ApigeeX\Entity\RatePlanInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\PropertyValidatorInterface;
use Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\SerializerAwarePropertyValidatorTrait;
use PHPUnit\Framework\Assert;

class RatePlanDetailsPropertyValidator implements PropertyValidatorInterface, \Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\SerializerAwarePropertyValidatorInterface
{
    use SerializerAwarePropertyValidatorTrait {
        setEntitySerializer as private traitSetEntitySerializer;
    }

    /** @var \Apigee\Edge\Tests\Api\ApigeeX\EntitySerializer\PropertyValidator\OrganizationProfileEntityReferencePropertyValidator */
    protected $orgPropValidator;

    /**
     * RatePlanDetailsPropertyValidator constructor.
     */
    public function __construct()
    {
        $this->orgPropValidator = new OrganizationProfileEntityReferencePropertyValidator();
    }

    /**
     * {@inheritdoc}
     */
    public function validate(\stdClass $input, \stdClass $output, EntityInterface $entity): void
    {
        if (!$entity instanceof RatePlanInterface) {
            return;
        }

        $expected = $input;
        Assert::assertEquals($output, $expected);

        $actual = json_decode($this->entitySerializer->serialize($entity->getRatePlanxFee(), 'json'));
        $expected = $input->{static::validatedProperty()};
        Assert::assertEquals($expected, $actual);

        $actual = json_decode($this->entitySerializer->serialize($entity->getFixedRecurringFee(), 'json'));
        $expected = $input->fixedRecurringFee;
        Assert::assertEquals($expected, $actual);

        $actual = json_decode($this->entitySerializer->serialize($entity->getRevenueShareRates(), 'json'));
        $expected = $input->revenueShareRates;
        Assert::assertEquals($expected, $actual);

        $actual = json_decode($this->entitySerializer->serialize($entity->getConsumptionPricingRates(), 'json'));
        $expected = $input->consumptionPricingRates;
        Assert::assertEquals($expected, $actual);
    }

    /**
     * {@inheritdoc}
     */
    public static function validatedProperty(): string
    {
        return 'ratePlanxFee';
    }

    /**
     * {@inheritdoc}
     */
    public function setEntitySerializer(\Apigee\Edge\Serializer\EntitySerializerInterface $entitySerializer): void
    {
        $this->traitSetEntitySerializer($entitySerializer);
        $this->orgPropValidator->setEntitySerializer($entitySerializer);
    }
}
