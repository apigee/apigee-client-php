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
use Apigee\Edge\Api\Monetization\Entity\OrganizationAwareEntityInterface;
use PHPUnit\Framework\Assert;

class OrganizationPropertyValidator implements EntityReferencePropertyValidatorInterface, SerializerAwarePropertyValidatorInterface
{
    use SerializerAwarePropertyValidatorTrait;

    /**
     * @inheritdoc
     */
    public function validate(\stdClass $input, \stdClass $output, EntityInterface $entity): void
    {
        if (!$entity instanceof OrganizationAwareEntityInterface) {
            return;
        }
        Assert::assertEquals($output->{$this->validatedProperty()}, (object) ['id' => $entity->getOrganization()->id()]);

        $actual = json_decode($this->entitySerializer->serialize($entity->getOrganization(), 'json'));
        $expected = $input->{$this->validatedProperty()};
        // If the organization is a nested property the address information is
        // missing from that.
        // @see \Apigee\Edge\Api\Monetization\Entity\OrganizationAwareEntity
        if (!property_exists($expected, 'address')) {
            unset($actual->address);
        }
        Assert::assertEquals($expected, $actual);
    }

    /**
     * @inheritdoc
     */
    public function validatedProperty(): string
    {
        return 'organization';
    }
}
