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

use Apigee\Edge\Api\Monetization\Entity\Property\CurrencyPropertyInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\SerializerAwarePropertyValidatorTrait;
use PHPUnit\Framework\Assert;

class CurrencyEntityReferencePropertyValidator implements \Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\RemoveIfPropertyValidPropertyValidatorInterface, \Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\SerializerAwarePropertyValidatorInterface
{
    use SerializerAwarePropertyValidatorTrait {
        setEntitySerializer as private traitSetEntitySerializer;
    }

    /** @var \Apigee\Edge\Tests\Api\Monetization\EntitySerializer\PropertyValidator\OrganizationProfileEntityReferencePropertyValidator */
    protected $orgPropValidator;

    /**
     * CurrencyPropertyValidator constructor.
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
        if (!$entity instanceof CurrencyPropertyInterface) {
            return;
        }
        Assert::assertEquals($output->{static::validatedProperty()}, (object) ['id' => $input->{static::validatedProperty()}->id]);

        // Validate the nested company object.
        $actual = json_decode($this->entitySerializer->serialize($entity->getCurrency(), 'json'));
        $expected = clone $input->{static::validatedProperty()};
        // We validate these separately.
        unset($actual->organization);
        unset($expected->organization);
        Assert::assertEquals($expected, $actual);

        // Validate the nested organization profile inside the company object.
        $output = json_decode($this->entitySerializer->serialize($entity->getCurrency()->getOrganization(), 'json'));

        $this->orgPropValidator->validate($input->{static::validatedProperty()}, $output, $entity->getCurrency()->getOrganization());
    }

    /**
     * @inheritdoc
     */
    public static function validatedProperty(): string
    {
        return 'currency';
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
