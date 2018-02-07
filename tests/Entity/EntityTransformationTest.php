<?php

namespace Apigee\Edge\Tests\Entity;

use Apigee\Edge\Entity\EntityDenormalizer;
use Apigee\Edge\Entity\EntityNormalizer;
use Apigee\Edge\Tests\Test\Mock\Entity as MockEntity;
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
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @group normalizer
 * @group denormalizer
 * @group offline
 * @small
 */
class EntityTransformationTest extends TestCase
{
    /** @var \Apigee\Edge\Entity\EntityNormalizer */
    protected static $normalizer;

    /** @var \Apigee\Edge\Entity\EntityDenormalizer */
    protected static $denormalizer;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$normalizer = new EntityNormalizer();
        static::$denormalizer = new EntityDenormalizer();
    }

    public function testNormalize()
    {
        $normalized = static::$normalizer->normalize(new MockEntity());
        $this->assertTrue(true === $normalized->bool);
        $this->assertTrue(2 === $normalized->int);
        $this->assertTrue(0 === $normalized->zero);
        $this->assertTrue(2.2 === $normalized->double);
        $this->assertTrue('string' === $normalized->string);
        $this->assertTrue('' === $normalized->emptyString);
        $this->assertEquals('foo', $normalized->attributesProperty[0]->name);
        $this->assertEquals('bar', $normalized->attributesProperty[0]->value);
        $this->assertEquals('foo', $normalized->credentialProduct->apiproduct);
        $this->assertEquals('bar', $normalized->credentialProduct->status);
        $this->assertEquals('foo', $normalized->propertiesProperty->property[0]->name);
        $this->assertEquals('bar', $normalized->propertiesProperty->property[0]->value);
        $this->assertNotEmpty($normalized->appCredential);
        $this->assertEquals('consumerKey', $normalized->appCredential[0]->consumerKey);
        $this->assertEquals('foo', $normalized->appCredential[0]->apiProducts[0]->apiproduct);
        $this->assertEquals('foo', $normalized->appCredential[0]->attributes[0]->name);

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
        /** @var \Apigee\Edge\Tests\Test\Mock\Entity $object */
        $object = static::$denormalizer->denormalize($normalized, MockEntity::class);
        $this->assertTrue(true === $object->isBool());
        $this->assertTrue(2 === $object->getInt());
        $this->assertTrue(0 === $object->getZero());
        $this->assertTrue(2.2 === $object->getDouble());
        $this->assertTrue('string' === $object->getString());
        $this->assertTrue('' === $object->getEmptyString());
        $this->assertEquals('bar', $object->getAttributesProperty()->getValue('foo'));
        $this->assertEquals('foo', $object->getCredentialProduct()->getApiproduct());
        $this->assertEquals('bar', $object->getCredentialProduct()->getStatus());
        $this->assertEquals('bar', $object->getPropertiesProperty()->getValue('foo'));
        $this->assertNotEmpty($object->getAppCredential());
        $this->assertEquals('consumerKey', $object->getAppCredential()[0]->getConsumerKey());
        $this->assertEquals('foo', $object->getAppCredential()[0]->getApiProducts()[0]->getApiproduct());
        $this->assertEquals('bar', $object->getAppCredential()[0]->getAttributeValue('foo'));
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
