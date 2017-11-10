<?php

namespace Apigee\Edge\Exception;

/**
 * Class EntityNotFoundException.
 *
 * @package Apigee\Edge\Exception
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class EntityNotFoundException extends \Exception
{
    /**
     * EntityNotFoundException constructor.
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
