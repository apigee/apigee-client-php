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

namespace Apigee\Edge\PropertyAccess;

use Apigee\Edge\Exception\UnexpectedValueException;
use Apigee\Edge\Exception\UninitializedPropertyException;

use function get_class;
use function gettype;

use ReflectionObject;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use TypeError;

/**
 * Extra features for Symfony's property accessor.
 */
final class PropertyAccessorDecorator implements PropertyAccessorInterface
{
    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * PropertyAccessorDecorator constructor.
     *
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue(&$objectOrArray, $propertyPath, $value): void
    {
        try {
            $this->propertyAccessor->setValue($objectOrArray, $propertyPath, $value);
        } catch (InvalidArgumentException|TypeError $exception) {
            // Auto-retry, try to pass the value as variable-length arguments to
            // the setter method.
            if (is_object($objectOrArray) && is_array($value)) {
                $setter = null;
                // Support setPropertyName() and propertyName() setters.
                foreach (['set' . ucfirst((string) $propertyPath), (string) $propertyPath] as $methodName) {
                    if (method_exists($objectOrArray, $methodName)) {
                        $setter = $methodName;
                        break;
                    }
                }

                if (null === $setter) {
                    throw new TypeError("Setter method not found for {$propertyPath} property.", 0, $exception);
                }

                try {
                    if (empty($value)) {
                        // Clear the value of the property.
                        $objectOrArray->{$setter}();
                    } else {
                        $objectOrArray->{$setter}(...$value);
                    }
                } catch (TypeError $typeError) {
                    self::processTypeErrorOnSetValue($typeError->getMessage(), $typeError->getTrace(), 0, (string) $typeError);

                    // Rethrow the exception if it could not be transformed
                    // to an invalid argument exception.
                    throw $typeError;
                }
            } else {
                throw $exception;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($objectOrArray, $propertyPath): mixed
    {
        try {
            $value = $this->propertyAccessor->getValue($objectOrArray, $propertyPath);
        } catch (TypeError $error) {
            // Make sure it is an object.
            if (is_object($objectOrArray)) {
                self::processTypeErrorOnGetValue($objectOrArray, (string) $propertyPath, $error);
            }

            // Rethrow the exception if it could not be transformed to something
            // else.
            throw $error;
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable($objectOrArray, $propertyPath): bool
    {
        return $this->propertyAccessor->isWritable($objectOrArray, $propertyPath);
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable($objectOrArray, $propertyPath): bool
    {
        return $this->propertyAccessor->isReadable($objectOrArray, $propertyPath);
    }

    /**
     * Processes type error exception on value get.
     *
     * Throws better, more meaningful exception if a value is uninitialised
     * or initialized with an incorrect value on an object.
     *
     * Based on PropertyAccessor::throwInvalidArgumentException().
     *
     * @param object $object
     * @param string $property
     * @param TypeError $error
     *
     * @see \Symfony\Component\PropertyAccess\PropertyAccessor::throwInvalidArgumentException()
     */
    private static function processTypeErrorOnGetValue($object, string $property, TypeError $error): void
    {
        if (0 !== strpos($error->getMessage(), 'Return value of ')) {
            return;
        }

        $pos = strpos($error->getMessage(), $delim = 'must be of the type ') ?: (strpos($error->getMessage(), $delim = 'must be an instance of ') ?: strpos($error->getMessage(), $delim = 'must implement interface '));
        if (false !== $pos) {
            $ro = new ReflectionObject($object);
            $rp = $ro->getProperty($property);
            $rp->setAccessible(true);
            $pos += strlen($delim);
            $actualValue = $rp->getValue($object);
            $expectedType = substr($error->getMessage(), $pos, (int) strpos($error->getMessage(), ',', $pos) - $pos);

            if (null === $actualValue) {
                throw new UninitializedPropertyException($object, $property, $expectedType);
            }

            $actualType = \is_object($actualValue) ? get_class($actualValue) : gettype($actualValue);

            // Until we are using strongly typed variables this should not happen.
            throw new UnexpectedValueException($object, $property, $expectedType, $actualType);
        }
    }

    /**
     * Processes type error exception on value set.
     *
     * Copy-paste of PropertyAccessor::throwInvalidArgumentException()
     * because it is private.
     *
     * @param $message
     * @param (array|int|string)[][] $trace
     * @param $i
     * @param $propertyPath
     * @param $previous
     *
     * @see \Symfony\Component\PropertyAccess\PropertyAccessor::throwInvalidArgumentException()
     *
     * @psalm-suppress PossiblyFalseOperand
     * @psalm-suppress PossiblyFalseArgument
     *
     * @psalm-param list<array{args?: array, class?: class-string, file?: string, function?: string, line?: int, type?: '->'|'::'}> $trace
     * @psalm-param 0 $i
     */
    private static function processTypeErrorOnSetValue(string $message, array $trace, int $i, string $propertyPath, $previous = null): void
    {
        if (!isset($trace[$i]['file']) || __FILE__ !== $trace[$i]['file']) {
            return;
        }
        if (preg_match('/^\S+::\S+\(\): Argument #\d+ \(\$\S+\) must be of type (\S+), (\S+) given/', $message, $matches)) {
            [, $expectedType, $actualType] = $matches;

            throw new InvalidArgumentException(sprintf('Expected argument of type "%s", "%s" given at property path "%s".', $expectedType, 'NULL' === $actualType ? 'null' : $actualType, $propertyPath), 0, $previous);
        }
        if (preg_match('/^Cannot assign (\S+) to property \S+::\$\S+ of type (\S+)$/', $message, $matches)) {
            [, $actualType, $expectedType] = $matches;

            throw new InvalidArgumentException(sprintf('Expected argument of type "%s", "%s" given at property path "%s".', $expectedType, 'NULL' === $actualType ? 'null' : $actualType, $propertyPath), 0, $previous);
        }
    }
}
