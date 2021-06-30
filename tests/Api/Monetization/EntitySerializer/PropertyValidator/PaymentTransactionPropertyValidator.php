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

use Apigee\Edge\Api\Monetization\Entity\BalanceInterface;
use Apigee\Edge\Api\Monetization\Entity\DeveloperCategory;
use Apigee\Edge\Api\Monetization\Structure\DeveloperPaymentTransaction;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\LegalEntitySerializerValidator;
use Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\SerializerAwarePropertyValidatorInterface;
use Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\SerializerAwarePropertyValidatorTrait;
use PHPUnit\Framework\Assert;

/**
 * Even if payment transactions are not entities we extend the entity
 * reference property validator to remove it from the validated object if
 * validation passes here.
 */
class PaymentTransactionPropertyValidator implements \Apigee\Edge\Tests\Test\EntitySerializer\PropertyValidator\RemoveIfPropertyValidPropertyValidatorInterface, SerializerAwarePropertyValidatorInterface
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
        if (!$entity instanceof BalanceInterface || null === $entity->getTransaction()) {
            return;
        }
        $expected = clone $input->{static::validatedProperty()}->developer;

        /* @var \Apigee\Edge\Api\Monetization\Entity\LegalEntity $lentity */
        if ($entity->getTransaction() instanceof DeveloperPaymentTransaction) {
            $lentity = $entity->getTransaction()->getDeveloper();
        } else {
            $lentity = $entity->getTransaction()->getCompany();
        }

        // Workaround for general validation logic.
        // In case of transactions we do not need to send them back to Apigee
        // Edge only parse them. Therefore we do not expect only the developer
        // id gets available in the serialized object.
        if ($lentity->getDeveloperCategory()) {
            // Still validate that we parsed all info properly.
            Assert::assertEquals($expected->developerCategory->description, $lentity->getDeveloperCategory()->getDescription());
            Assert::assertEquals($expected->developerCategory->name, $lentity->getDeveloperCategory()->getName());
            $expected->developerCategory = (object) ['id' => $lentity->getDeveloperCategory()->id()];
            $lentity->setDeveloperCategory(new DeveloperCategory(['id' => $lentity->getDeveloperCategory()->id()]));
        }
        // Same applies to the organization reference, but we can workaround
        // this a different way.
        // TODO Validate organization object before it gets removed.
        unset($expected->organization);
        $ro = new \ReflectionObject($lentity);
        $orgProp = $ro->getProperty('organization');
        $orgProp->setAccessible(true);
        // Only way to clear organization.
        $orgProp->setValue($lentity, null);
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
        return 'transaction';
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
