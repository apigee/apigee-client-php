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

use LogicException;

/**
 * Thrown when a property has not been initialized.
 *
 * Base class is LogicException because this is what a NormalizerInterface
 * implementation can throw according to its doc.
 *
 * @see \Symfony\Component\Serializer\Normalizer\NormalizerInterface::normalize()
 */
class UninitializedPropertyException extends LogicException implements ApiClientException
{
    /**
     * UninitializedPropertyException constructor.
     *
     * @param object $object
     * @param string $property
     * @param string $expectedType
     * @param string $message
     */
    public function __construct($object, string $property, string $expectedType, $message = 'Property "@property" has not been initialized on instance of @class class. Expected type: @expected.')
    {
        $message = strtr($message, [
            '@class' => get_class($object),
            '@property' => $property,
            '@expected' => class_exists($expectedType) || interface_exists($expectedType) ? 'instance of "' . $expectedType . '"' : '"' . $expectedType . '"',
        ]);
        parent::__construct($message);
    }
}
