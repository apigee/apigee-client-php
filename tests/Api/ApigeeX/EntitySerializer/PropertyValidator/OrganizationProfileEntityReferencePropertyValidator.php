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

use Apigee\Edge\Api\Monetization\Entity\Property\OrganizationPropertyInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\RemoveIfPropertyValidPropertyValidatorInterface;
use Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\SerializerAwarePropertyValidatorTrait;
use PHPUnit\Framework\Assert;
use stdClass;

class OrganizationProfileEntityReferencePropertyValidator implements RemoveIfPropertyValidPropertyValidatorInterface, \Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\SerializerAwarePropertyValidatorInterface
{
    use SerializerAwarePropertyValidatorTrait;

    /**
     * {@inheritdoc}
     */
    public function validate(stdClass $input, stdClass $output, EntityInterface $entity): void
    {
        if (!$entity instanceof OrganizationPropertyInterface) {
            return;
        }

        if (!isset($input->{static::validatedProperty()}) && null === $entity->getOrganization()) {
            return;
        }

        Assert::assertEquals($output->{static::validatedProperty()}, (object) ['id' => $entity->getOrganization()->id()]);

        $actual = json_decode($this->entitySerializer->serialize($entity->getOrganization(), 'json'));
        $expected = $input->{static::validatedProperty()};
        // If the organization is a nested property the address information is
        // missing from that.
        // @see \Apigee\Edge\Api\Monetization\Entity\OrganizationAwareEntity
        if (!property_exists($expected, 'address')) {
            unset($actual->address);
        }
        Assert::assertEquals($expected, $actual);
    }

    /**
     * {@inheritdoc}
     */
    public static function validatedProperty(): string
    {
        return 'organization';
    }
}
