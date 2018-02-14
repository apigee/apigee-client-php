<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
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
