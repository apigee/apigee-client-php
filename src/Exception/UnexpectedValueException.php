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

namespace Apigee\Edge\Exception;

use Symfony\Component\Serializer\Exception\UnexpectedValueException as BaseUnexpectedValueException;

/**
 * Thrown when a value of a property does not match with its expected type.
 */
class UnexpectedValueException extends BaseUnexpectedValueException implements ApiClientException
{
    /**
     * UnexpectedValueException constructor.
     *
     * @param object $object
     * @param string $property
     * @param string $expectedType
     * @param string $actualType
     */
    public function __construct($object, string $property, string $expectedType, string $actualType)
    {
        $message = sprintf(
            'Invalid value returned for %s property on instance of %s class. Expected type "%s", got "%s".',
            $property,
            get_class($object),
            $expectedType,
            $actualType
        );
        parent::__construct($message);
    }
}
