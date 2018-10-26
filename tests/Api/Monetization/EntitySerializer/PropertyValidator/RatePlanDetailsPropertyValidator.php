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

use Apigee\Edge\Api\Monetization\Entity\RatePlanInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\PropertyValidatorInterface;
use Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\SerializerAwarePropertyValidatorTrait;
use PHPUnit\Framework\Assert;

class RatePlanDetailsPropertyValidator implements PropertyValidatorInterface, \Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\SerializerAwarePropertyValidatorInterface
{
    use SerializerAwarePropertyValidatorTrait {
        setEntitySerializer as private traitSetEntitySerializer;
    }

    /** @var \Apigee\Edge\Tests\Api\Monetization\EntitySerializer\PropertyValidator\OrganizationProfileEntityReferencePropertyValidator */
    protected $orgPropValidator;

    /**
     * RatePlanDetailsPropertyValidator constructor.
     */
    public function __construct()
    {
        $this->orgPropValidator = new OrganizationProfileEntityReferencePropertyValidator();
    }

    /**
     * @inheritdoc
     */
    public function validate(\stdClass $input, \stdClass $output, EntityInterface $entity): void
    {
        if (!$entity instanceof RatePlanInterface) {
            return;
        }

        $expected = $input->{$this->validatedProperty()};
        foreach ($entity->getRatePlanDetails() as $key => $detail) {
            $expected[$key]->currency = (object) ['id' => $detail->getCurrency()->id()];
            $expected[$key]->organization = (object) ['id' => $detail->getOrganization()->id()];
        }
        Assert::assertEquals($output->{static::validatedProperty()}, $expected);

        $actual = json_decode($this->entitySerializer->serialize($entity->getRatePlanDetails(), 'json'));
        $expected = $input->{static::validatedProperty()};
        // We validate these separately.
        unset($actual->organization);
        unset($expected->organization);
        Assert::assertEquals($expected, $actual);

        foreach ($entity->getRatePlanDetails() as $key => $detail) {
            // Validate the nested organization profile inside the company object.
            $output = json_decode($this->entitySerializer->serialize($detail->getOrganization(), 'json'));
            $this->orgPropValidator->validate($input->{static::validatedProperty()}[$key]->organization, $output, $detail->getOrganization());
        }
    }

    /**
     * @inheritdoc
     */
    public static function validatedProperty(): string
    {
        return 'ratePlanDetails';
    }

    /**
     * @inheritDoc
     */
    public function setEntitySerializer(\Apigee\Edge\Serializer\EntitySerializerInterface $entitySerializer): void
    {
        $this->traitSetEntitySerializer($entitySerializer);
        $this->orgPropValidator->setEntitySerializer($entitySerializer);
    }
}
