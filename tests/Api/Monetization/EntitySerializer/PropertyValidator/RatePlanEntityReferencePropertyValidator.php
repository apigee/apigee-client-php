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

use Apigee\Edge\Api\Monetization\Entity\AcceptedRatePlanInterface;
use Apigee\Edge\Api\Monetization\Entity\EntityInterface;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\RatePlanSerializerValidator;
use PHPUnit\Framework\Assert;

class RatePlanEntityReferencePropertyValidator implements EntityReferencePropertyValidatorInterface, SerializerAwarePropertyValidatorInterface
{
    use SerializerAwarePropertyValidatorTrait {
        setEntitySerializer as private traitSetEntitySerializer;
    }

    private $ratePlanEntitySerializerValidator;

    /**
     * RatePlanEntityReferencePropertyValidator constructor.
     */
    public function __construct()
    {
        $this->ratePlanEntitySerializerValidator = new RatePlanSerializerValidator();
    }

    /**
     * @inheritdoc
     */
    public function validate(\stdClass $input, \stdClass $output, EntityInterface $entity): void
    {
        if (!$entity instanceof AcceptedRatePlanInterface) {
            return;
        }

        Assert::assertEquals($output->{static::validatedProperty()}, (object) ['id' => $input->{static::validatedProperty()}->id]);

        // Validate the nested rate plan object.
        $expected = clone $input->{static::validatedProperty()};
        $this->ratePlanEntitySerializerValidator->validate($expected, $entity->getRatePlan());
    }

    /**
     * @inheritdoc
     */
    public static function validatedProperty(): string
    {
        return 'ratePlan';
    }

    /**
     * @inheritDoc
     */
    public function setEntitySerializer(\Apigee\Edge\Serializer\EntitySerializerInterface $entitySerializer): void
    {
        $this->traitSetEntitySerializer($entitySerializer);
        $this->ratePlanEntitySerializerValidator = new RatePlanSerializerValidator($entitySerializer);
    }
}
