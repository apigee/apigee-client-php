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

namespace Apigee\Edge\Tests\Structure;

use Apigee\Edge\Denormalizer\AttributesPropertyDenormalizer;
use Apigee\Edge\Normalizer\KeyValueMapNormalizer;
use Apigee\Edge\Structure\AttributesProperty;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory as ComparisonFactory;

/**
 * Class AttributesPropertyTransformationTest.
 *
 * Validates that our custom attributes normalizer and denormalizer are working properly.
 *
 * @group structure
 * @group normalizer
 * @group denormalizer
 *
 * @small
 */
class AttributesPropertyTransformationTest extends TestCase
{
    /** @var KeyValueMapNormalizer */
    protected static $normalizer;

    /** @var AttributesPropertyDenormalizer */
    protected static $denormalizer;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$normalizer = new KeyValueMapNormalizer();
        static::$denormalizer = new AttributesPropertyDenormalizer();
    }

    public function testNormalize()
    {
        $object = new AttributesProperty(['foo' => 'bar']);
        $this->assertEquals('bar', $object->getValue('foo'));
        $normalized = static::$normalizer->normalize($object);
        $this->assertArrayHasKey('0', $normalized);
        $this->assertEquals('foo', $normalized[0]->name);
        $this->assertEquals('bar', $normalized[0]->value);

        return $normalized;
    }

    /**
     * @depends testNormalize
     *
     * @param array $normalized
     */
    public function testDenormalize(array $normalized): void
    {
        /** @var AttributesProperty $object */
        $object = static::$denormalizer->denormalize($normalized, AttributesProperty::class);
        $this->assertTrue($object->has('foo'));
        $this->assertEquals('bar', $object->getValue('foo'));
        $renormalized = static::$normalizer->normalize($object);
        $compFactory = new ComparisonFactory();
        $comparator = $compFactory->getComparatorFor($normalized, $renormalized);
        try {
            $comparator->assertEquals($normalized, $renormalized);
        } catch (ComparisonFailure $failure) {
            $this->fail($failure->toString());
        }
    }

    public function testPerfectInput(): void
    {
        $nested = ['foo' => 'bar'];
        $object = static::$denormalizer->denormalize($nested, AttributesProperty::class);
        $this->assertTrue($object->has('foo'));
        $this->assertEquals('bar', $object->getValue('foo'));
    }
}
