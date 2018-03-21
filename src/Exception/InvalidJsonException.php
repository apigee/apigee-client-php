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

use Http\Message\Formatter;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class InvalidJsonException.
 */
class InvalidJsonException extends ApiResponseException
{
    /** @var string */
    protected $jsonErrorMessage;

    /**
     * InvalidJsonException constructor.
     *
     * @param string $jsonErrorMessage
     * @param ResponseInterface $response
     * @param RequestInterface $request
     * @param Formatter|null $formatter
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $jsonErrorMessage,
        ResponseInterface $response,
        RequestInterface $request,
        Formatter $formatter = null,
        \Throwable $previous = null
    ) {
        $this->jsonErrorMessage = $jsonErrorMessage;
        parent::__construct($response, $request, $jsonErrorMessage, 0, $previous, $formatter);
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return sprintf("%s\n%s", $this->jsonErrorMessage, parent::__toString());
    }

    /**
     * @return string
     */
    public function getJsonErrorMessage()
    {
        return $this->jsonErrorMessage;
    }
}
