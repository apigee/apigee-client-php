<?php

/**
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
