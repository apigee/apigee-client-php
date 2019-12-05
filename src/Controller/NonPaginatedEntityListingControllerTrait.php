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

namespace Apigee\Edge\Controller;

use Psr\Http\Message\ResponseInterface;

/**
 * Trait NonPaginatedEntityListingControllerTrait.
 *
 * @see \Apigee\Edge\Controller\NonPaginatedEntityListingControllerInterface
 */
trait NonPaginatedEntityListingControllerTrait
{
    use BaseEndpointAwareControllerTrait;
    use ClientAwareControllerTrait;

    /**
     * @inheritdoc
     */
    public function getEntities(): array
    {
        $query_params = [
            'expand' => 'true',
        ];
        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($query_params));
        $response = $this->getClient()->get($uri);
        $responseArray = $this->responseToArray($response);
        // Ignore entity type key from response, ex.: apiProduct.
        $responseArray = reset($responseArray);

        return $this->responseArrayToArrayOfEntities($responseArray);
    }

    /**
     * @inheritdoc
     */
    abstract protected function responseToArray(ResponseInterface $response, bool $expandCompatibility = false): array;

    /**
     * @inheritdoc
     */
    abstract protected function responseArrayToArrayOfEntities(array $responseArray, string $keyGetter = 'id'): array;
}
