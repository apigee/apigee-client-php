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
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\ApiPackageSerializerValidator;
use Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\RemoveIfPropertyValidPropertyValidatorInterface;
use Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\SerializerAwarePropertyValidatorTrait;
use PHPUnit\Framework\Assert;
use stdClass;

class ApiPackageEntityReferencePropertyValidator implements RemoveIfPropertyValidPropertyValidatorInterface, \Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\SerializerAwarePropertyValidatorInterface
{
    use SerializerAwarePropertyValidatorTrait {
        setEntitySerializer as private traitSetEntitySerializer;
    }

    private $apiPackageEntitySerializerValidator;

    /**
     * LegalEntityReferencePropertyValidator constructor.
     */
    public function __construct()
    {
        $this->apiPackageEntitySerializerValidator = new ApiPackageSerializerValidator();
    }

    /**
     * {@inheritdoc}
     */
    public function validate(stdClass $input, stdClass $output, EntityInterface $entity): void
    {
        // At this moment only rate plans contain entity references.
        if (!$entity instanceof RatePlanInterface) {
            return;
        }

        Assert::assertEquals($output->{static::validatedProperty()}, (object) ['id' => $input->{static::validatedProperty()}->id]);

        // Validate the nested company object.
        /** @var \Apigee\Edge\Api\Monetization\Entity\LegalEntityInterface $lentity */
        $expected = clone $input->{static::validatedProperty()};
        $this->apiPackageEntitySerializerValidator->validate($expected, $entity->getPackage());
    }

    /**
     * {@inheritdoc}
     */
    public static function validatedProperty(): string
    {
        return 'monetizationPackage';
    }

    /**
     * {@inheritdoc}
     */
    public function setEntitySerializer(EntitySerializerInterface $entitySerializer): void
    {
        $this->traitSetEntitySerializer($entitySerializer);
        $this->apiPackageEntitySerializerValidator = new ApiPackageSerializerValidator($entitySerializer);
    }
}
