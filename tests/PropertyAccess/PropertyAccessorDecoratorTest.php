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
use Apigee\Edge\Exception\UninitializedPropertyException;
use Apigee\Edge\PropertyAccess\PropertyAccessorDecorator;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\Tests\PropertyAccessorTest;

class PropertyAccessorDecoratorTest extends PropertyAccessorTest
{
    /**
     * @var \Apigee\Edge\PropertyAccess\PropertyAccessorDecorator
     */
    private $propertyAccessor;

    /** @var object */
    private static $testObj;

    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$testObj = new class() {
            /** @var string[] */
            private $shouldBeAStringArray;

            /** @var string */
            private $shouldBeAString;

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
        };
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->propertyAccessor = new PropertyAccessorDecorator(new PropertyAccessor());
        // Killing some kittens but still better than copy-pasting all tests
        // from parent. Our decorator must work the same as the decorated
        // class.
        $ro = new \ReflectionClass(PropertyAccessorTest::class);
        $property = $ro->getProperty('propertyAccessor');
        $property->setAccessible(true);
        $property->setValue($this, $this->propertyAccessor);
    }

    /**
     * @dataProvider exceptionsToGetOnGetValue
     */
    public function testGetValueWithInvalidReturns(string $property, string $expectedException, string $expectedExceptionMessageRegexp): void
    {
        try {
            $this->propertyAccessor->getValue(static::$testObj, $property);
        } catch (\Exception $exception) {
            $this->assertInstanceOf($expectedException, $exception);
            $this->assertRegExp($expectedExceptionMessageRegexp, $exception->getMessage());
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
    }

    /**
     * @dataProvider exceptionsToGetOnSetValue
     */
    public function testSetValueWithInvalidVariableLengthArgs(string $property, $value, string $expectedException, string $expectedExceptionMessage): void
    {
        try {
            $this->propertyAccessor->setValue(static::$testObj, $property, $value);
        } catch (\Exception $exception) {
            $this->assertInstanceOf($expectedException, $exception);
            $this->assertEquals($expectedExceptionMessage, $exception->getMessage());
        } finally {
            if (!isset($exception)) {
                $this->fail('An exception should have been thrown.');
            }
        }
    }

    public function exceptionsToGetOnGetValue(): array
    {
        return [
            ['shouldBeAStringArray', UninitializedPropertyException::class, '/^Property "shouldBeAStringArray" has not been initialized on instance of class@anonymous.* class. Expected type: "array".$/'],
            ['shouldBeAString', UnexpectedValueException::class, '/Invalid value returned for shouldBeAString property on instance of class@anonymous.* class. Expected type "string", got "stdClass".$/'],
        ];
    }

    public function exceptionsToGetOnSetValue(): array
    {
        return [
            ['shouldBeAStringArray', [], InvalidArgumentException::class, 'Expected argument of type "string", "array" given.'],
            ['shouldBeAStringArray', [null], InvalidArgumentException::class, 'Expected argument of type "string", "NULL" given.'],
        ];
    }
}
