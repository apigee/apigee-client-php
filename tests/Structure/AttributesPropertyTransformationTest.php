<?php

namespace Apigee\Edge\Tests\Structure;

use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Structure\AttributesPropertyDenormalizer;
use Apigee\Edge\Structure\AttributesPropertyNormalizer;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory as ComparisonFactory;

/**
 * Class AttributesPropertyTransformationTest.
 *
 * Validates that our custom attributes normalizer and denormalizer are working properly.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @group structure
 * @group normalizer
 * @group denormalizer
 * @group offline
 * @small
 */
class AttributesPropertyTransformationTest extends TestCase
{
    /** @var \Apigee\Edge\Structure\AttributesPropertyNormalizer */
    protected static $normalizer;

    /** @var \Apigee\Edge\Structure\AttributesPropertyDenormalizer */
    protected static $denormalizer;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$normalizer = new AttributesPropertyNormalizer();
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
        /** @var \Apigee\Edge\Structure\AttributesProperty $object */
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
