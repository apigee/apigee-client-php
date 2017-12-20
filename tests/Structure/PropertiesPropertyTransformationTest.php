<?php

namespace Apigee\Edge\Tests\Structure;

use Apigee\Edge\Structure\PropertiesProperty;
use Apigee\Edge\Structure\PropertiesPropertyDenormalizer;
use Apigee\Edge\Structure\PropertiesPropertyNormalizer;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory as ComparisonFactory;

/**
 * Class PropertiesPropertyTransformationTest.
 *
 * Validates that our custom properties normalizer and denormalizer are working properly.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @group structure
 * @group normalizer
 * @group denormalizer
 * @group offline
 * @small
 */
class PropertiesPropertyTransformationTest extends TestCase
{
    /** @var \Apigee\Edge\Structure\PropertiesPropertyNormalizer */
    protected static $normalizer;

    /** @var \Apigee\Edge\Structure\PropertiesPropertyDenormalizer */
    protected static $denormalizer;

    /**
     * @inheritdoc
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
