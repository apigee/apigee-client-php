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
use Apigee\Edge\Entity\Property\AttributesPropertyInterface;
use Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\PropertyValidatorInterface;
use Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\SerializerAwarePropertyValidatorTrait;
use PHPUnit\Framework\Assert;

class CustomAttributesPropertyValidator implements PropertyValidatorInterface, \Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\SerializerAwarePropertyValidatorInterface
{
    use SerializerAwarePropertyValidatorTrait;

    /**
     * {@inheritdoc}
     */
    public function validate(\stdClass $input, \stdClass $output, EntityInterface $entity): void
    {
        if (!$entity instanceof AttributesPropertyInterface) {
            return;
        }

        $expected = [];
        foreach ($entity->getAttributes()->values() as $name => $value) {
            $expected[] = (object) [
                'name' => $name,
                'value' => $value,
            ];
        }

        // This property may not be set in nested objects.
        if (!isset($input->{static::validatedProperty()}) && empty($expected)) {
            return;
        }

        Assert::assertEquals($output->{static::validatedProperty()}, $expected);

        $actual = json_decode($this->entitySerializer->serialize($entity->getAttributes(), 'json'));
        $expected = $input->{static::validatedProperty()};
        // We do not store these extra, Monetization only additions on
        // attributes.
        // @see \Apigee\Edge\Api\Monetization\Entity\LegalEntity
        foreach ($expected as &$item) {
            unset($item->id);
            unset($item->parentId);
        }
        Assert::assertEquals($expected, $actual);
    }

    /**
     * {@inheritdoc}
     */
    public static function validatedProperty(): string
    {
        return 'customAttributes';
    }
}
