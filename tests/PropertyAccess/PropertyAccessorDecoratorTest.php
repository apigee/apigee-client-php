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

namespace Apigee\Edge\Tests\PropertyAccess;

use Apigee\Edge\Exception\UnexpectedValueException;
use Apigee\Edge\PropertyAccess\PropertyAccessorDecorator;
use Exception;

use const PHP_VERSION_ID;

use ReflectionClass;
use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\Tests\PropertyAccessorTest;
use TypeError;

class PropertyAccessorDecoratorTest extends PropertyAccessorTest
{
    use PhpUnitBcBridgeTrait;

    /**
     * @var PropertyAccessorDecorator
     */
    private $propertyAccessor;

    /** @var object */
    private static $testObj;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$testObj = new class {
            /** @var string[] */
            private $shouldBeAStringArray;

            /** @var string */
            private $shouldBeAString;

            /** @var string */
            private $queryBuilderParam;

            public function __construct()
            {
                // Fake invalid value.
                $this->shouldBeAString = (object) [];
            }

            /**
             * @return string[]
             */
            public function getShouldBeAStringArray(): array
            {
                return $this->shouldBeAStringArray;
            }

            /**
             * @param string ...$shouldBeAStringArray
             */
            public function setShouldBeAStringArray(string ...$shouldBeAStringArray): void
            {
                $this->shouldBeAStringArray = $shouldBeAStringArray;
            }

            /**
             * @return string
             */
            public function getShouldBeAString(): string
            {
                return $this->shouldBeAString;
            }

            /**
             * @param string $shouldBeAString
             */
            public function setShouldBeAString(string $shouldBeAString): void
            {
                $this->shouldBeAString = $shouldBeAString;
            }

            /**
             * @return string
             */
            public function getQueryBuilderParam(): string
            {
                return $this->queryBuilderParam;
            }

            /**
             * @param string $queryBuilderParam
             */
            public function queryBuilderParam(string $queryBuilderParam): void
            {
                $this->queryBuilderParam = $queryBuilderParam;
            }
        };
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->propertyAccessor = new PropertyAccessorDecorator(new PropertyAccessor());
        // Killing some kittens but still better than copy-pasting all tests
        // from parent. Our decorator must work the same as the decorated
        // class.
        $ro = new ReflectionClass(PropertyAccessorTest::class);
        $property = $ro->getProperty('propertyAccessor');
        $property->setAccessible(true);
        $property->setValue($this, $this->propertyAccessor);
    }

    /**
     * Overrides the parent test method.
     * Uses the DataProvider from the parent class.
     *
     * @dataProvider Symfony\Component\PropertyAccess\Tests\PropertyAccessorTest::voidAccessorProvider
     */
    public function testIgnoreVoidAccessor(string $property, mixed $value): void
    {
        // This assumes the test logic is also valid for your decorator.
        parent::testIgnoreVoidAccessor($property, $value);
    }

    /**
     * @dataProvider exceptionsToGetOnGetValue
     */
    public function testGetValueWithInvalidReturns(string $property, string $expectedException, ?string $expectedExceptionMessageRegexp = null): void
    {
        try {
            $this->propertyAccessor->getValue(static::$testObj, $property);
        } catch (Exception|TypeError $exception) {
            $this->assertInstanceOf($expectedException, $exception);
            if (null !== $expectedExceptionMessageRegexp) {
                $this->assertMatchesRegularExpression($expectedExceptionMessageRegexp, $exception->getMessage());
            }
        } finally {
            if (!isset($exception)) {
                $this->fail('An exception should have been thrown.');
            }
        }
    }

    public function testSetValueArrayValues(): void
    {
        // Make sure all array values set.
        $this->propertyAccessor->setValue(static::$testObj, 'shouldBeAStringArray', ['foo', 'bar']);
        $this->assertEquals('foo', static::$testObj->getShouldBeAStringArray()[0]);
        $this->assertEquals('bar', static::$testObj->getShouldBeAStringArray()[1]);
        // Previously set values should be removed.
        $this->propertyAccessor->setValue(static::$testObj, 'shouldBeAStringArray', []);
        $this->assertEmpty(static::$testObj->getShouldBeAStringArray());
    }

    /**
     * @dataProvider exceptionsToGetOnSetValue
     */
    public function testSetValueWithInvalidVariableLengthArgs(string $property, $value, string $expectedException, string $expectedExceptionMessage): void
    {
        try {
            $this->propertyAccessor->setValue(static::$testObj, $property, $value);
        } catch (Exception|TypeError $exception) {
            $this->assertInstanceOf($expectedException, $exception);
            $this->assertMatchesRegularExpression($expectedExceptionMessage, $exception->getMessage());
        } finally {
            if (!isset($exception)) {
                $this->fail('An exception should have been thrown.');
            }
        }
    }

    public function testSetValueOnQueryBuilderParameter(): void
    {
        $this->propertyAccessor->setValue(static::$testObj, 'queryBuilderParam', 'foo');
        $this->assertEquals('foo', static::$testObj->getQueryBuilderParam());
    }

    public function exceptionsToGetOnGetValue(): array
    {
        // It seems the upstream issue has been fixed, throwing an
        // unexpected value exception for this case is no longer needed.
        // https://github.com/symfony/property-access/commit/e1a6c91c0007e45bc1beba929c76548ca9fe8a85

        $shouldBeAString = ['shouldBeAStringArray', AccessException::class];
        if (PHP_VERSION_ID < 80000) {
            return [
                $shouldBeAString,
                ['shouldBeAString', UnexpectedValueException::class, '/Invalid value returned for shouldBeAString property on instance of class@anonymous.* class. Expected type "string", got "stdClass".$/'],
            ];
        }

        return [
            $shouldBeAString,
            ['shouldBeAString', TypeError::class, '/Return value must be of type string, stdClass returned/'],
        ];
    }

    public function exceptionsToGetOnSetValue(): array
    {
        // We will be checking the php version as the exception returned in php 8 is different from php 7.
        if (PHP_VERSION_ID < 80000) {
            return [
                ['shouldBeAStringArray', [null], InvalidArgumentException::class, '/^Expected argument of type "string", "null" given/'],
            ];
        }

        return [
            ['shouldBeAStringArray', [null], TypeError::class, '/Argument #1 must be of type string, null given/'],
        ];
    }

    /* * COMPATIBILITY OVERRIDES (PHPUnit 9+ vs Symfony 7.4+) *
     *
     * Note: We use `object|array` type hint because Symfony 7.4+ enforces it.
     * We use `$value = null` (optional) because some tests in 7.4 don't pass the 3rd argument.
     * We use `...func_get_args()` to pass exactly the arguments received to the parent.
     */

    /**
     * @dataProvider getValidReadPropertyPaths
     */
    public function testGetValue(object|array $objectOrArray, string $path, $value = null): void
    {
        parent::testGetValue(...func_get_args());
    }

    /**
     * @dataProvider getPathsWithMissingProperty
     */
    public function testGetValueThrowsExceptionIfPropertyNotFound(object|array $objectOrArray, string $path): void
    {
        parent::testGetValueThrowsExceptionIfPropertyNotFound(...func_get_args());
    }

    /**
     * @dataProvider getPathsWithMissingProperty
     */
    public function testGetValueReturnsNullIfPropertyNotFoundAndExceptionIsDisabled(object|array $objectOrArray, string $path): void
    {
        parent::testGetValueReturnsNullIfPropertyNotFoundAndExceptionIsDisabled(...func_get_args());
    }

    /**
     * @dataProvider getPathsWithMissingIndex
     */
    public function testGetValueThrowsNoExceptionIfIndexNotFound(object|array $objectOrArray, string $path): void
    {
        parent::testGetValueThrowsNoExceptionIfIndexNotFound(...func_get_args());
    }

    /**
     * @dataProvider getPathsWithMissingIndex
     */
    public function testGetValueThrowsExceptionIfIndexNotFoundAndIndexExceptionsEnabled(object|array $objectOrArray, string $path): void
    {
        parent::testGetValueThrowsExceptionIfIndexNotFoundAndIndexExceptionsEnabled(...func_get_args());
    }

    /**
     * @dataProvider getValidWritePropertyPaths
     */
    public function testSetValue(object|array $objectOrArray, string $path, $value = null): void
    {
        parent::testSetValue(...func_get_args());
    }

    /**
     * @dataProvider getPathsWithMissingProperty
     */
    public function testSetValueThrowsExceptionIfPropertyNotFound(object|array $objectOrArray, string $path): void
    {
        parent::testSetValueThrowsExceptionIfPropertyNotFound(...func_get_args());
    }

    /**
     * @dataProvider getPathsWithMissingIndex
     */
    public function testSetValueThrowsNoExceptionIfIndexNotFound(object|array $objectOrArray, string $path): void
    {
        parent::testSetValueThrowsNoExceptionIfIndexNotFound(...func_get_args());
    }

    /**
     * @dataProvider getPathsWithMissingIndex
     */
    public function testSetValueThrowsNoExceptionIfIndexNotFoundAndIndexExceptionsEnabled(object|array $objectOrArray, string $path): void
    {
        parent::testSetValueThrowsNoExceptionIfIndexNotFoundAndIndexExceptionsEnabled(...func_get_args());
    }

    /**
     * @dataProvider getValidReadPropertyPaths
     */
    public function testIsReadable(object|array $objectOrArray, string $path, $value = null): void
    {
        parent::testIsReadable(...func_get_args());
    }

    /**
     * @dataProvider getPathsWithMissingProperty
     */
    public function testIsReadableReturnsFalseIfPropertyNotFound(object|array $objectOrArray, string $path): void
    {
        parent::testIsReadableReturnsFalseIfPropertyNotFound(...func_get_args());
    }

    /**
     * @dataProvider getPathsWithMissingIndex
     */
    public function testIsReadableReturnsTrueIfIndexNotFound(object|array $objectOrArray, string $path): void
    {
        parent::testIsReadableReturnsTrueIfIndexNotFound(...func_get_args());
    }

    /**
     * @dataProvider getPathsWithMissingIndex
     */
    public function testIsReadableReturnsFalseIfIndexNotFoundAndIndexExceptionsEnabled(object|array $objectOrArray, string $path): void
    {
        parent::testIsReadableReturnsFalseIfIndexNotFoundAndIndexExceptionsEnabled(...func_get_args());
    }

    /**
     * @dataProvider getValidWritePropertyPaths
     */
    public function testIsWritable(object|array $objectOrArray, string $path, $value = null): void
    {
        parent::testIsWritable(...func_get_args());
    }

    /**
     * @dataProvider getPathsWithMissingProperty
     */
    public function testIsWritableReturnsFalseIfPropertyNotFound(object|array $objectOrArray, string $path): void
    {
        parent::testIsWritableReturnsFalseIfPropertyNotFound(...func_get_args());
    }

    /**
     * @dataProvider getPathsWithMissingIndex
     */
    public function testIsWritableReturnsTrueIfIndexNotFound(object|array $objectOrArray, string $path): void
    {
        parent::testIsWritableReturnsTrueIfIndexNotFound(...func_get_args());
    }

    /**
     * @dataProvider getPathsWithMissingIndex
     */
    public function testIsWritableReturnsTrueIfIndexNotFoundAndIndexExceptionsEnabled(object|array $objectOrArray, string $path): void
    {
        parent::testIsWritableReturnsTrueIfIndexNotFoundAndIndexExceptionsEnabled(...func_get_args());
    }

    /**
     * @dataProvider getNullSafeIndexPaths
     */
    public function testNullSafeIndexWithThrowOnInvalidIndex(object|array $objectOrArray, string $path, $value = null): void
    {
        parent::testNullSafeIndexWithThrowOnInvalidIndex(...func_get_args());
    }

    /**
     * @dataProvider getReferenceChainObjectsForSetValue
     */
    public function testSetValueForReferenceChainIssue($object, $path, $value): void
    {
        parent::testSetValueForReferenceChainIssue($object, $path, $value);
    }

    /**
     * @dataProvider getReferenceChainObjectsForIsWritable
     */
    public function testIsWritableForReferenceChainIssue($object, $path, $value): void
    {
        parent::testIsWritableForReferenceChainIssue($object, $path, $value);
    }

    /* * PHP 8.4 SKIPS * */

    public function testIsWritableWithAsymmetricVisibility(): void
    {
        if (PHP_VERSION_ID < 80400) {
            $this->markTestSkipped('Requires PHP 8.4');
        }
        parent::testIsWritableWithAsymmetricVisibility();
    }

    public function testIsReadableWithAsymmetricVisibility(): void
    {
        if (PHP_VERSION_ID < 80400) {
            $this->markTestSkipped('Requires PHP 8.4');
        }
        parent::testIsReadableWithAsymmetricVisibility();
    }

    public function testSetValueWithAsymmetricVisibility(string $propertyPath = '', ?string $expectedException = null): void
    {
        if (PHP_VERSION_ID < 80400) {
            $this->markTestSkipped('Requires PHP 8.4');
        }
        parent::testSetValueWithAsymmetricVisibility($propertyPath, $expectedException);
    }
}
