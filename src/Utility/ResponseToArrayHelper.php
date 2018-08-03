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

namespace Apigee\Edge\Utility;

use Apigee\Edge\Controller\ClientAwareControllerTrait;
use Apigee\Edge\Exception\ApiResponseException;
use Apigee\Edge\Exception\InvalidJsonException;
use Psr\Http\Message\ResponseInterface;

trait ResponseToArrayHelper
{
    use ClientAwareControllerTrait;

    /**
     * Decodes an Apigee Edge API response to an associative array.
     *
     * The SDK only works with JSON responses, but let's be prepared for the unexpected.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @throws \RuntimeException If response can not be decoded, because the input format is unknown.
     * @throws \Apigee\Edge\Exception\InvalidJsonException If there was an error with decoding a JSON response.
     *
     * @return array
     */
    protected function responseToArray(ResponseInterface $response): array
    {
        if ($response->getHeaderLine('Content-Type') &&
            0 === strpos($response->getHeaderLine('Content-Type'), 'application/json')) {
            try {
                return (array) $this->jsonDecoder->decode((string) $response->getBody(), 'json');
            } catch (\UnexpectedValueException $e) {
                throw new InvalidJsonException(
                    $e->getMessage(),
                    $response,
                    $this->client->getJournal()->getLastRequest()
                );
            }
        }
        throw new ApiResponseException(
            $response,
            $this->client->getJournal()->getLastRequest(),
            sprintf('Unable to parse response with %s type. Response body: %s', $response->getHeaderLine('Content-Type') ?: 'unknown', (string) $response->getBody())
        );
    }
}
