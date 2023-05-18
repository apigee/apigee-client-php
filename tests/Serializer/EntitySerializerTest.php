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

namespace Apigee\Edge\Tests\Serializer;

use Apigee\Edge\Api\Management\Normalizer\AppCredentialNormalizer;
use Apigee\Edge\Denormalizer\AttributesPropertyDenormalizer;
use Apigee\Edge\Denormalizer\CredentialProductDenormalizer;
use Apigee\Edge\Denormalizer\EdgeDateDenormalizer;
use Apigee\Edge\Denormalizer\PropertiesPropertyDenormalizer;
use Apigee\Edge\Normalizer\EdgeDateNormalizer;
use Apigee\Edge\Normalizer\PropertiesPropertyNormalizer;
use Apigee\Edge\Serializer\EntitySerializer;
use Apigee\Edge\Tests\Test\Entity\MockEntity;
use GuzzleHttp\Psr7\Response;
use function GuzzleHttp\Psr7\stream_for;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory as ComparisonFactory;

/**
 * Class EntityTransformationTest.
 *
 * Validates that our custom entity normalizer and denormalizer are working
 * properly and they can normalize and denormalize all types of data
 * (scalar and objects) to Apigee Edge's structure and back to our internal
 * structure. Also validates that the two components is compatible with
 * each other.
 *
 * @group normalizer
 * @group denormalizer
 * @small
 */
class EntitySerializerTest extends TestCase
{
    /** @var \Apigee\Edge\Serializer\EntitySerializer */
    protected static $serializer;

    /**
     * {@inheritdoc}
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
        static::$serializer = new EntitySerializer($normalizers);
    }

    public function testNormalize()
    {
        $entity = new MockEntity();
        $normalized = static::$serializer->normalize($entity);
        $this->assertTrue(true === $normalized->bool);
        $this->assertTrue(2 === $normalized->int);
        $this->assertTrue(0 === $normalized->zero);
        $this->assertTrue(2.0 === $normalized->double);
        $this->assertTrue('string' === $normalized->string);
        $this->assertTrue('' === $normalized->emptyString);
        $this->assertEquals('foo', $normalized->attributes[0]->name);
        $this->assertEquals('bar', $normalized->attributes[0]->value);
        $this->assertEquals('foo', $normalized->credentialProduct->apiproduct);
        $this->assertEquals('bar', $normalized->credentialProduct->status);
        $this->assertEquals('foo', $normalized->properties->property[0]->name);
        $this->assertEquals('bar', $normalized->properties->property[0]->value);
        $this->assertNotEmpty($normalized->appCredential);
        $this->assertEquals('consumerKey', $normalized->appCredential[0]->consumerKey);
        $this->assertEquals(-1, $normalized->appCredential[0]->expiresAt);
        $this->assertEquals('foo', $normalized->appCredential[0]->apiProducts[0]->apiproduct);
        $this->assertEquals('foo', $normalized->appCredential[0]->attributes[0]->name);
        $this->assertObjectNotHasAttribute('date', $normalized);
        $date = new \DateTimeImmutable();
        $entity->setDate($date);
        $normalized = static::$serializer->normalize($entity);
        $this->assertEquals($entity->getDate()->getTimestamp() * 1000, $normalized->date);

        return $normalized;
    }

    /**
     * @depends testNormalize
     *
     * @param \stdClass $normalized
     */
    public function testDenormalize(mixed $normalized): void
    {
        // Set value of this nullable value to ensure that a special condition is triggered in the EntityDenormalizer.
        $normalized->nullable = null;
        /** @var \Apigee\Edge\Tests\Test\Entity\MockEntity $object */
        $object = static::$serializer->denormalize($normalized, MockEntity::class);
        $this->assertTrue(true === $object->isBool());
        $this->assertTrue(2 === $object->getInt());
        $this->assertTrue(0 === $object->getZero());
        $this->assertTrue(2.0 === $object->getDouble());
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
        $renormalized = static::$serializer->normalize($object);
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

    public function testSetPropertiesFromResponseWithValidValues(): void
    {
        $entity = new MockEntity();
        $original = clone $entity;
        $response = (object) [
            // Has setter + getter.
            'int' => 1,
            // Has setter + isser.
            'bool' => true,
            'variableLengthArgs' => ['first', 'second'],
            'propertyWithoutSetter' => 1,
            'propertyWithoutGetter' => true,
        ];
        static::$serializer->setPropertiesFromResponse(new Response('200', ['Content-type' => 'application/json'], stream_for(json_encode($response))), $entity);
        // These properties should change.
        $this->assertEquals($response->int, $entity->getInt());
        $this->assertEquals($response->bool, $entity->isBool());
        $this->assertEquals($response->variableLengthArgs, $entity->getVariableLengthArgs());
        // These properties should not change.
        $this->assertEquals($original->getPropertyWithoutSetter(), $entity->getPropertyWithoutSetter());
        $roOriginal = new \ReflectionObject($original);
        $roEntity = new \ReflectionObject($entity);
        /** @var \ReflectionProperty $originalProperty */
        /** @var \ReflectionProperty $entityProperty */
        $originalProperty = $roOriginal->getProperty('propertyWithoutGetter');
        $originalProperty->setAccessible(true);
        $entityProperty = $roEntity->getProperty('propertyWithoutGetter');
        $entityProperty->setAccessible(true);
        $this->assertEquals($originalProperty->getValue($original), $entityProperty->getValue($entity));

        // Ensure that value of a property that uses variable-length
        // parameters in its setter can be cleared by passing an empty array.
        $response = (object) [
            'variableLengthArgs' => [],
        ];
        static::$serializer->setPropertiesFromResponse(new Response('200', ['Content-type' => 'application/json'], stream_for(json_encode($response))), $entity);
        $this->assertEmpty($entity->getVariableLengthArgs());
    }

    public function testSetPropertiesFromResponseWithInvalidValue(): void
    {
        if (\PHP_VERSION_ID < 80000) {
            $this->expectException('\Symfony\Component\Serializer\Exception\NotNormalizableValueException');
            $this->expectExceptionMessage('Expected argument of type "string", "object" given.');
        } else {
            $this->expectException(\TypeError::class);
            $this->expectExceptionMessage('Argument #1 must be of type string, stdClass given');
        }

        $entity = new MockEntity();
        $response = (object) [
            // Only string acceptable.
            // Scalar values automatically gets casted to strings - this is
            // what we can not fix.
            'variableLengthArgs' => [(object) []],
        ];
        static::$serializer->setPropertiesFromResponse(new Response('200', ['Content-type' => 'application/json'], stream_for(json_encode($response))), $entity);
    }
}
