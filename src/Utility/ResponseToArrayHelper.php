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
use RuntimeException;
use UnexpectedValueException;

trait ResponseToArrayHelper
{
    use ClientAwareControllerTrait;
    use JsonDecoderAwareTrait;

    /**
     * Decodes an Apigee Edge API response to an associative array.
     *
     * The SDK only works with JSON responses, but let's be prepared for the unexpected.
     *
     * @param ResponseInterface $response
     * @param bool $expandCompatability
     *   If the API response requires backwards compatibility with the way Edge
     *   formats it's responses.
     *
     * @see For reference on $expandCompatability, see the structure of
     *   expand=false query parameter on the Hybrid documentation:
     *   https://docs.apigee.com/hybrid/beta2/reference/apis/unsupported-apis
     *
     * @throws RuntimeException If response can not be decoded, because the input format is unknown.
     * @throws InvalidJsonException If there was an error with decoding a JSON response.
     *
     * @return array
     */
    protected function responseToArray(ResponseInterface $response, bool $expandCompatibility = false): array
    {
        if ($response->getHeaderLine('Content-Type')
            && 0 === strpos($response->getHeaderLine('Content-Type'), 'application/json')) {
            try {
                $decoded = (array) $this->jsonDecoder()->decode((string) $response->getBody(), 'json');

                if ($expandCompatibility) {
                    $decoded = $this->normalizeExpandFalseForHybrid($decoded);
                }

                return $decoded;
            } catch (UnexpectedValueException $e) {
                throw new InvalidJsonException(
                    $e->getMessage(),
                    $response,
                    $this->getClient()->getJournal()->getLastRequest()
                );
            }
        }
        throw new ApiResponseException(
            $response,
            $this->getClient()->getJournal()->getLastRequest(),
            sprintf('Unable to parse response with %s type. Response body: %s', $response->getHeaderLine('Content-Type') ?: 'unknown', (string) $response->getBody())
        );
    }

    /**
     * Helper method to normalize a Hybrid response.
     *
     * @see ResponseToArrayHelper::responseToArray()
     *
     * @param array $responseArray
     *   The decoded response array.
     *
     * @return array
     *   The response array normalized.
     */
    protected function normalizeExpandFalseForHybrid(array $responseArray): array
    {
        // If empty, no further processing is needed.
        if (empty($responseArray)) {
            return $responseArray;
        }

        // Ignore entity type key from response, ex.: apiProduct.
        $responseArray = reset($responseArray);

        // Return an array with the value of the first property of each item.
        return array_map(function ($item) {
            $item = (array) $item;

            return reset($item);
        }, $responseArray);
    }
}
