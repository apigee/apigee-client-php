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
 * Thrown when a response was received but the request itself failed.
 */
class ApiResponseException extends ApiRequestException
{
    /**
     * Field in the response JSON that contains the error code.
     *
     * @var string
     */
    protected $errorCodeResponseField = 'code';

    /**
     * Field in the response JSON that contains the error description.
     *
     * @var string
     */
    protected $errorDescriptionResponseField = 'message';

    /** @var \Psr\Http\Message\ResponseInterface */
    private $response;

    /** @var null|string */
    private $edgeErrorCode;

    /**
     * ApiResponseException constructor.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Psr\Http\Message\RequestInterface $request
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     * @param \Http\Message\Formatter|null $formatter
     */
    public function __construct(
        ResponseInterface $response,
        RequestInterface $request,
        string $message = '',
        int $code = 0,
        \Throwable $previous = null,
        Formatter $formatter = null
    ) {
        $this->response = $response;
        $message = $message ?: $response->getReasonPhrase();
        // Try to parse Edge error message and error code from the response body.
        $contentTypeHeader = $response->getHeaderLine('Content-Type');
        $this->errorCodeResponseField = 'code';
        $this->errorDescriptionResponseField = 'message';
        if ($contentTypeHeader && false !== strpos($contentTypeHeader, 'application/json')) {
            $array = json_decode((string) $response->getBody(), true);
            if (JSON_ERROR_NONE === json_last_error()) {
                if ($this->errorCodeResponseField && array_key_exists($this->errorCodeResponseField, $array)) {
                    $this->edgeErrorCode = $array[$this->errorCodeResponseField];
                }
                if ($this->errorDescriptionResponseField && array_key_exists($this->errorDescriptionResponseField, $array)) {
                    $message = $array[$this->errorDescriptionResponseField];
                }
            }
        }
        parent::__construct($request, $message, $code, $previous, $formatter);
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return sprintf(
            "Request:\n%s\nResponse:\n%s\n",
            $this->formatter->formatRequest($this->request),
            $this->formatter->formatResponse($this->response)
        );
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @return null|string
     */
    public function getEdgeErrorCode(): ?string
    {
        return $this->edgeErrorCode;
    }
}
