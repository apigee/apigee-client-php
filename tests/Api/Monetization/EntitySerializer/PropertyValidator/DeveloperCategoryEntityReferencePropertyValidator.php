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

use Apigee\Edge\Api\Monetization\Entity\LegalEntityInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\SerializerAwarePropertyValidatorInterface;
use Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\SerializerAwarePropertyValidatorTrait;
use PHPUnit\Framework\Assert;

class DeveloperCategoryEntityReferencePropertyValidator implements \Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\RemoveIfPropertyValidPropertyValidatorInterface, SerializerAwarePropertyValidatorInterface
{
    use SerializerAwarePropertyValidatorTrait;

    /**
     * @inheritdoc
     */
    public function validate(\stdClass $input, \stdClass $output, EntityInterface $entity): void
    {
        if (!$entity instanceof LegalEntityInterface) {
            return;
        }

        Assert::assertEquals($output->{static::validatedProperty()}, (object) ['id' => $input->{static::validatedProperty()}->id]);

        $actual = json_decode($this->entitySerializer->serialize($entity->getDeveloperCategory(), 'json'));
        $expected = $input->{static::validatedProperty()};
        Assert::assertEquals($expected, $actual);
    }

    /**
     * @inheritdoc
     */
    public static function validatedProperty(): string
    {
        return 'developerCategory';
    }
}
