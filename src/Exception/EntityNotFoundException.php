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
 * Class EntityNotFoundException.
 */
class EntityNotFoundException extends \Exception
{
    /**
     * EntityNotFoundException constructor.
     *
     * @param string $fqcn Fully wualified name of the class.
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($fqcn, $code = 0, \Throwable $previous = null)
    {
        $message = sprintf('%s entity not found.', $fqcn);
        parent::__construct($message, $code, $previous);
    }
}
