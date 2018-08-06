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

namespace Apigee\Edge\Tests\Entity;

use Apigee\Edge\Api\Management\Normalizer\AppCredentialNormalizer;
use Apigee\Edge\Denormalizer\AttributesPropertyDenormalizer;
use Apigee\Edge\Denormalizer\CredentialProductDenormalizer;
use Apigee\Edge\Denormalizer\EdgeDateDenormalizer;
use Apigee\Edge\Denormalizer\PropertiesPropertyDenormalizer;
use Apigee\Edge\Normalizer\EdgeDateNormalizer;
use Apigee\Edge\Normalizer\PropertiesPropertyNormalizer;
use Apigee\Edge\Serializer\EntitySerializer;
use Apigee\Edge\Tests\Test\Entity\MockEntity;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory as ComparisonFactory;

/**
 * Class EntityTransformationTest.
 *
 * Validates that our custom entity normalizer and denormalizer are working properly and they can normalize and
 * denormalize all types of data (scalar and objects) to Edge's structure and back to our internal structure.
 * Also validates that the two components is compatible with each other.
 *
 *
 * @group normalizer
 * @group denormalizer
 * @group offline
 * @small
 */
class EntitySerializerTest extends TestCase
{
    /** @var \Apigee\Edge\Normalizer\EntityNormalizer */
    protected static $normalizer;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $normalizers = [
            // The order of these (de)normalizers matters when one of them is applicable
            // to multiple classes. Ex.: KVM (de)normalizer is applicable to all KVM subclasses.
            new EdgeDateNormalizer(),
            new EdgeDateDenormalizer(),
            new AppCredentialNormalizer(),
            new AttributesPropertyDenormalizer(),
            new PropertiesPropertyNormalizer(),
            new PropertiesPropertyDenormalizer(),
            new CredentialProductDenormalizer(),
        ];
        static::$normalizer = new EntitySerializer($normalizers);
    }

    public function testNormalize()
    {
        $entity = new MockEntity();
        $normalized = static::$normalizer->normalize($entity);
        $this->assertTrue(true === $normalized->bool);
        $this->assertTrue(2 === $normalized->int);
        $this->assertTrue(0 === $normalized->zero);
        $this->assertTrue(2.2 === $normalized->double);
        $this->assertTrue('string' === $normalized->string);
        $this->assertTrue('' === $normalized->emptyString);
        $this->assertEquals('foo', $normalized->attributes[0]->name);
        $this->assertEquals('bar', $normalized->attributes[0]->value);
        $this->assertEquals('foo', $normalized->credentialProduct->apiproduct);
        $this->assertEquals('bar', $normalized->credentialProduct->status);
        $this->assertArrayHasKey('foo', $normalized->properties);
        $this->assertEquals('bar', $normalized->properties['foo']);
        $this->assertNotEmpty($normalized->appCredential);
        $this->assertEquals('consumerKey', $normalized->appCredential[0]->consumerKey);
        $this->assertEquals(-1, $normalized->appCredential[0]->expiresAt);
        $this->assertEquals('foo', $normalized->appCredential[0]->apiProducts[0]->apiproduct);
        $this->assertEquals('foo', $normalized->appCredential[0]->attributes[0]->name);
        $this->assertObjectNotHasAttribute('date', $normalized);
        $date = new \DateTimeImmutable();
        $entity->setDate($date);
        $normalized = static::$normalizer->normalize($entity);
        $this->assertEquals($entity->getDate()->getTimestamp() * 1000, $normalized->date);

        return $normalized;
    }

    /**
     * @depends testNormalize
     *
     * @param \stdClass $normalized
     */
    public function testDenormalize(\stdClass $normalized): void
    {
        // Set value of this nullable value to ensure that a special condition is triggered in the EntityDenormalizer.
        $normalized->nullable = null;
        /** @var \Apigee\Edge\Tests\Test\Entity\MockEntity $object */
        $object = static::$normalizer->denormalize($normalized, MockEntity::class);
        $this->assertTrue(true === $object->isBool());
        $this->assertTrue(2 === $object->getInt());
        $this->assertTrue(0 === $object->getZero());
        $this->assertTrue(2.2 === $object->getDouble());
        $this->assertTrue('string' === $object->getString());
        $this->assertTrue('' === $object->getEmptyString());
        $this->assertEquals('bar', $object->getAttributeValue('foo'));
        $this->assertEquals('foo', $object->getCredentialProduct()->getApiproduct());
        $this->assertEquals('bar', $object->getCredentialProduct()->getStatus());
        $this->assertEquals('bar', $object->getPropertyValue('foo'));
        $this->assertNotEmpty($object->getAppCredential());
        $this->assertEquals('consumerKey', $object->getAppCredential()[0]->getConsumerKey());
        $this->assertNull($object->getAppCredential()[0]->getExpiresAt());
        $this->assertEquals('foo', $object->getAppCredential()[0]->getApiProducts()[0]->getApiproduct());
        $this->assertEquals('bar', $object->getAppCredential()[0]->getAttributeValue('foo'));
        $this->assertEquals(new \DateTimeImmutable('@' . $normalized->date / 1000), $object->getDate());
        $renormalized = static::$normalizer->normalize($object);
        // Unset it to ensure that the two objects can be equal.
        unset($normalized->nullable);
        $compFactory = new ComparisonFactory();
        $comparator = $compFactory->getComparatorFor($normalized, $renormalized);
        try {
            $comparator->assertEquals($normalized, $renormalized);
        } catch (ComparisonFailure $failure) {
            $this->fail($failure->toString());
        }
    }
}
