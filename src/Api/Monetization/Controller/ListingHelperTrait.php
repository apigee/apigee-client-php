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

namespace Apigee\Edge\Api\Monetization\Controller;

use Apigee\Edge\Controller\ClientAwareControllerTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

trait ListingHelperTrait
{
    use ClientAwareControllerTrait;

    protected function listEntities(UriInterface $uri): array
    {
        return $this->responseArrayToArrayOfEntities($this->getRawList($uri));
    }

    /**
     * Returns a raw API response as an array of a listing API endpoint.
     *
     * @param UriInterface $uri
     *   URI of the endpoint where the request should be sent.
     *
     * @return array
     *   API response as an array.
     */
    protected function getRawList(UriInterface $uri): array
    {
        $response = $this->getClient()->get($uri);
        $responseArray = $this->responseToArray($response);

        // Ignore entity type key from response, ex.: product.
        return reset($responseArray);
    }

    abstract protected function responseToArray(ResponseInterface $response, bool $expandCompatibility = false): array;

    abstract protected function responseArrayToArrayOfEntities(array $responseArray, string $keyGetter = 'id'): array;
}
