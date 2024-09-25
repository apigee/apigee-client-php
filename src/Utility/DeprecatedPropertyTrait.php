<?php

/*
 * Copyright 2019 Google LLC
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

namespace Apigee\Edge\Utility;

use LogicException;

/**
 * Trait DeprecatedPropertyTrait.
 *
 * Provides a standard way to announce and access deprecated properties.
 * To use, include the trait and declare a `deprecatedProperties` property,
 * with the following format:
 *
 * @code
 * protected $deprecatedProperties = [
 *     'deprecatedProperty' => 'replacementProperty',
 * ];
 *
 * @endcode
 */
trait DeprecatedPropertyTrait
{
    /**
     * Allows access to deprecated/removed properties.
     */
    public function __get($name)
    {
        $removedBeforeVersion = '3.0.0';

        if (!isset($this->deprecatedProperties)) {
            throw new LogicException('The deprecatedProperties property must be defined to use this trait.');
        }

        if (isset($this->deprecatedProperties[$name])) {
            $replacement = $this->deprecatedProperties[$name];
            $className = static::class;

            if (empty($replacement) || !property_exists($className, $replacement)) {
                // The property has no replacement (it does not exist anymore) or has an invalid/missing replacement.
                $message = "The property $name is deprecated in $className and no replacement has been defined.";
                @trigger_error($message, E_USER_DEPRECATED);
                throw new LogicException($message);
            }

            // If the property does have a replacement.
            @trigger_error("The property $name is deprecated in $className and will be removed before $removedBeforeVersion", E_USER_DEPRECATED);

            return $this->{$replacement};
        }
    }
}
