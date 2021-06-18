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

use Apigee\Edge\Denormalizer\PropertiesPropertyDenormalizer;
use Apigee\Edge\Normalizer\PropertiesPropertyNormalizer;
use Apigee\Edge\Structure\PropertiesProperty;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory as ComparisonFactory;

/**
 * Class PropertiesPropertyTransformationTest.
 *
 * Validates that our custom properties normalizer and denormalizer are working properly.
 *
 * @group structure
 * @group normalizer
 * @group denormalizer
 * @small
 */
class PropertiesPropertyTransformationTest extends TestCase
{
    /** @var \Apigee\Edge\Normalizer\PropertiesPropertyNormalizer */
    protected static $normalizer;

    /** @var \Apigee\Edge\Denormalizer\PropertiesPropertyDenormalizer */
    protected static $denormalizer;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$normalizer = new PropertiesPropertyNormalizer();
        static::$denormalizer = new PropertiesPropertyDenormalizer();
    }

    public function testNormalize()
    {
        $object = new PropertiesProperty(['features.isCpsEnabled' => 'true']);
        $this->assertEquals('true', $object->getValue('features.isCpsEnabled'));
        $normalized = static::$normalizer->normalize($object);
        $this->assertObjectHasAttribute('property', $normalized);
        $this->assertArrayHasKey(0, $normalized->property);
        $this->assertEquals('features.isCpsEnabled', $normalized->property[0]->name);
        $this->assertEquals('true', $normalized->property[0]->value);

        return $normalized;
    }

    /**
     * @depends testNormalize
     *
     * @param \stdClass $normalized
     */
    public function testDenormalize(\stdClass $normalized): void
    {
        /** @var \Apigee\Edge\Structure\PropertiesProperty $object */
        $object = static::$denormalizer->denormalize($normalized, PropertiesProperty::class);
        $this->assertTrue($object->has('features.isCpsEnabled'));
        $this->assertEquals('true', $object->getValue('features.isCpsEnabled'));
        $renormalized = static::$normalizer->normalize($object);
        $compFactory = new ComparisonFactory();
        $comparator = $compFactory->getComparatorFor($normalized, $renormalized);
        try {
            $comparator->assertEquals($normalized, $renormalized);
        } catch (ComparisonFailure $failure) {
            $this->fail($failure->toString());
        }
    }
}
