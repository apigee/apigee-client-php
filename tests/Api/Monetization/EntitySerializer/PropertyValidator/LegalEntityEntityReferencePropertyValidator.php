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

use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\LegalEntitySerializerValidator;
use Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\SerializerAwarePropertyValidatorTrait;
use PHPUnit\Framework\Assert;

class LegalEntityEntityReferencePropertyValidator implements \Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\RemoveIfPropertyValidPropertyValidatorInterface, \Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\SerializerAwarePropertyValidatorInterface
{
    use SerializerAwarePropertyValidatorTrait {
        setEntitySerializer as private traitSetEntitySerializer;
    }

    private $legalEntitySerializerValidator;

    /**
     * LegalEntityReferencePropertyValidator constructor.
     */
    public function __construct()
    {
        $this->legalEntitySerializerValidator = new LegalEntitySerializerValidator();
    }

    /**
     * {@inheritdoc}
     */
    public function validate(\stdClass $input, \stdClass $output, EntityInterface $entity): void
    {
        if (!method_exists($entity, 'getCompany') && !method_exists($entity, 'getDeveloper')) {
            return;
        }

        Assert::assertEquals($output->{static::validatedProperty()}, (object) ['id' => $input->{static::validatedProperty()}->id]);

        // Validate the nested company object.
        /** @var \Apigee\Edge\Api\Monetization\Entity\LegalEntityInterface $lentity */
        $lentity = method_exists($entity, 'getCompany') ? $entity->getCompany() : $entity->getDeveloper();
        $expected = clone $input->{static::validatedProperty()};
        // These properties are missing (not returned by Apigee Edge) on a
        // nested legal entity object.
        if (empty($lentity->getAddresses())) {
            $expected->address = [];
        }
        if (empty($lentity->getAttributes()->values())) {
            $expected->customAttributes = [];
        }
        $this->legalEntitySerializerValidator->validate($expected, $lentity);
    }

    /**
     * {@inheritdoc}
     */
    public static function validatedProperty(): string
    {
        return 'developer';
    }

    /**
     * {@inheritdoc}
     */
    public function setEntitySerializer(\Apigee\Edge\Serializer\EntitySerializerInterface $entitySerializer): void
    {
        $this->traitSetEntitySerializer($entitySerializer);
        $this->legalEntitySerializerValidator = new LegalEntitySerializerValidator($entitySerializer);
    }
}
