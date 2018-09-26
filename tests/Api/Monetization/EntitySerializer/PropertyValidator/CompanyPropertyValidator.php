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

use Apigee\Edge\Api\Monetization\Entity\Developer;
use Apigee\Edge\Api\Monetization\Entity\EntityInterface;
use PHPUnit\Framework\Assert;

class CompanyPropertyValidator implements EntityReferencePropertyValidatorInterface, SerializerAwarePropertyValidatorInterface
{
    use SerializerAwarePropertyValidatorTrait {
        setEntitySerializer as private traitSetEntitySerializer;
    }

    /** @var \Apigee\Edge\Tests\Api\Monetization\EntitySerializer\PropertyValidator\OrganizationPropertyValidator */
    protected $orgPropValidator;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->orgPropValidator = new OrganizationPropertyValidator();
    }

    /**
     * @inheritdoc
     */
    public function validate(\stdClass $input, \stdClass $output, EntityInterface $entity): void
    {
        if (!$entity instanceof Developer) {
            return;
        }
        Assert::assertEquals($output->{static::validatedProperty()}, (object) ['id' => $input->{static::validatedProperty()}->id]);

        // Validate the nested company object.
        $actual = json_decode($this->entitySerializer->serialize($entity->getCompany(), 'json'));
        $expected = clone $input->{static::validatedProperty()};
        // These properties are not returned by Apigee Edge in this context.
        unset($actual->address);
        unset($actual->customAttributes);
        // We validate these separately.
        unset($actual->organization);
        unset($expected->organization);
        Assert::assertEquals($expected, $actual);

        $orgValidator = new OrganizationPropertyValidator();
        $orgValidator->setEntitySerializer($this->entitySerializer);

        // Validate the nested organization profile inside the company object.
        $output = json_decode($this->entitySerializer->serialize($entity->getCompany(), 'json'));
        $orgValidator->validate($input->{static::validatedProperty()}, $output, $entity->getCompany());
    }

    /**
     * @inheritdoc
     */
    public static function validatedProperty(): string
    {
        return 'parent';
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
