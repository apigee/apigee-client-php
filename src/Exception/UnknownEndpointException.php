<?php

namespace Apigee\Edge\Exception;

/**
 * Class UnknownEndpointException.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
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
