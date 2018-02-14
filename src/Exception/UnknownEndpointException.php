<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
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
        parent::__construct(sprintf('%s endpoint is unknown.'), 0, $previous);
    }
}
