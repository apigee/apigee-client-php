<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Exception;

use Http\Message\Formatter;
use Http\Message\Formatter\FullHttpMessageFormatter;
use Psr\Http\Message\RequestInterface;

/**
 * Exception for when a request failed, providing access to the failed request.
 *
 * This could be due to an invalid request, or one of the extending exceptions
 * for network errors or HTTP error responses.
 */
class ApiRequestException extends ApiException
{
    /** @var \Psr\Http\Message\RequestInterface */
    protected $request;

    /** @var \Http\Message\Formatter */
    protected $formatter;
    /**
     * @var null|\Throwable
     */
    private $previous;

    /**
     * ApiException constructor.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     * @param \Http\Message\Formatter|null $formatter
     */
    public function __construct(
        RequestInterface $request,
        string $message = '',
        int $code = 0,
        \Throwable $previous = null,
        Formatter $formatter = null
    ) {
        $this->request = $request;
        $this->previous = $previous;
        $this->formatter = $formatter ?: new FullHttpMessageFormatter();
        parent::__construct($message, $code, $previous);
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        $output = sprintf(
            "Request:\n%s\n",
            $this->formatter->formatRequest($this->request)
        );

        if ($this->previous) {
            $output .= sprintf("%s\n", $this->previous->getMessage());
        }

        return $output;
    }

    /**
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @return \Http\Message\Formatter
     */
    public function getFormatter(): Formatter
    {
        return $this->formatter;
    }
}
