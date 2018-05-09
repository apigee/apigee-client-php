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

/**
 * Class UnknownEndpointException.
 */
class UnknownEndpointException extends \InvalidArgumentException
{
    /**
     * UnknownEndpointException constructor.
     *
     * @param string $endpoint
     * @param \Throwable|null $previous
     */
    public function __construct($endpoint, \Throwable $previous = null)
    {
        parent::__construct("{$endpoint} endpoint is unknown.", 0, $previous);
    }
}
