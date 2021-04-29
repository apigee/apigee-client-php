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
    /** @var \Psr\Http\Message\ResponseInterface */
    private $response;

    /** @var string|null */
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
        list('code' => $errorCode, 'message' => $errorMessage) = $this->parseErrorResponse($response);
        $this->edgeErrorCode = $errorCode;
        $message = $errorMessage ?: $message;

        parent::__construct($request, $message, $code, $previous, $formatter);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $output = [
            get_called_class() . PHP_EOL,
            'Request:' . PHP_EOL . $this->formatter->formatRequest($this->request) . PHP_EOL,
            'Response:' . PHP_EOL . $this->formatter->formatResponse($this->response) . PHP_EOL,
            'Stack trace: ' . PHP_EOL . $this->getTraceAsString(),
        ];

        return implode(PHP_EOL, $output);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @return string|null
     */
    public function getEdgeErrorCode(): ?string
    {
        return $this->edgeErrorCode;
    }

    /**
     * Tries to extract Apigee Edge error code and message from a response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *   API response.
     *
     * @return array
     *   An associative array where keys are "code" and "message" and their
     *   values are either string or null.
     */
    private function parseErrorResponse(ResponseInterface $response): array
    {
        $error = [
            'code' => null,
            'message' => null,
        ];
        // Try to parse Edge error message and error code from the response body.
        $contentTypeHeader = $response->getHeaderLine('Content-Type');
        if ($contentTypeHeader && false !== strpos($contentTypeHeader, 'application/json')) {
            $array = json_decode((string) $response->getBody(), true);
            $array = is_array($array) ? $array : (array) $array;

            if (JSON_ERROR_NONE === json_last_error()) {
                if (array_key_exists('fault', $array)) {
                    $error['message'] = $array['fault']['faultstring'] ?? null;
                    $error['code'] = $array['fault']['detail']['errorcode'] ?? null;
                } else {
                    if (array_key_exists('code', $array)) {
                        $error['code'] = $array['code'];
                    } elseif (isset($array['error']['details'][0]['violations'][0]['type'])) {
                        // Hybrid returns the Edge error code here.
                        $error['code'] = $array['error']['details'][0]['violations'][0]['type'];
                    } elseif (isset($array['error']['code'])) {
                        // Fallback for Hybrid if the above is not set.
                        $error['code'] = $array['error']['code'];
                    }
                    if (array_key_exists('message', $array)) {
                        // It could happen that the returned message by
                        // Apigee Edge is also an array.
                        $error['message'] = is_array($array['message']) ? json_encode($array['message']) : $array['message'];
                    } elseif (isset($array['error']['message'])) {
                        $error['message'] = $array['error']['message'];
                        if (!empty($array['error']['status'])) {
                            $error['message'] = $array['error']['status'] . ': ' . $error['message'];
                        }
                    }
                }
            }
        }

        return $error;
    }
}
