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

namespace Apigee\Edge\Tests\Api\Monetization\EntitySerializer;

use Apigee\Edge\Api\Monetization\Entity\EntityInterface;
use Apigee\Edge\Serializer\EntitySerializer;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\PropertyValidator\PropertyValidatorsAwareValidatorInterface;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\PropertyValidator\PropertyValidatorTrait;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\PropertyValidator\SerializerAwarePropertyValidatorInterface;

class EntitySerializerValidator implements EntitySerializerValidatorInterface
{
    use PropertyValidatorTrait;

    /**
     * @var \Apigee\Edge\Serializer\EntitySerializerInterface
     */
    protected $serializer;

    /**
     * EntitySerializerValidator constructor.
     *
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface $serializer
     * @param array $propertyValidators
     */
    public function __construct(EntitySerializerInterface $serializer = null, array $propertyValidators = [])
    {
        $serializer = $serializer ?? new EntitySerializer();
        $this->serializer = $serializer;
        $this->propertyValidators = $propertyValidators;

        foreach ($this->propertyValidators as $validator) {
            if ($validator instanceof SerializerAwarePropertyValidatorInterface) {
                $validator->setEntitySerializer($this->serializer);
            }
        }

        foreach ($this->propertyValidators as $validator) {
            if ($validator instanceof PropertyValidatorsAwareValidatorInterface) {
                $validator->setPropertyValidators($this->propertyValidators);
            }
        }
    }

    public function validate(\stdClass $input, EntityInterface $entity): void
    {
        $output = json_decode($this->serializer->serialize($entity, 'json'));
        $this->propertyValidatorsValidate($input, $output, $entity);
    }
}
