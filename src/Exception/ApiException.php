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

use Http\Client\Exception;

/**
 * General exception class for API communication errors.
 */
class ApiException extends RuntimeException implements Exception
{
    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        // This is just a wrapper around the base class and if it contains a reference to the previous
        // exception we should display that as a string.
        if ($this->getPrevious()) {
            $output = [
                get_called_class() . PHP_EOL,
                (string) $this->getPrevious() . PHP_EOL,
                'Stack trace: ' . PHP_EOL . $this->getTraceAsString(),
            ];

            return implode(PHP_EOL, $output);
        }

        return parent::__toString();
    }
}
