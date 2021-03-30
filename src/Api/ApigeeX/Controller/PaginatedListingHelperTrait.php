<?php

/*
 * Copyright 2021 Google LLC
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

namespace Apigee\Edge\Api\ApigeeX\Controller;

use Psr\Http\Message\UriInterface;

trait PaginatedListingHelperTrait
{
    use ListingHelperTrait;

    protected function listAllEntities(UriInterface $uri): array
    {
        // Do not lose already set query parameters.
        $query_params = [];
        parse_str($uri->getQuery(), $query_params);
        $query_params['expand'] = 'true';

        // This makes easier testing and also creates a query parameter list
        // that is easy to read.
        ksort($query_params);

        return $this->listEntities($uri->withQuery(http_build_query($query_params)));
    }

    protected function listEntitiesInRange(UriInterface $uri, int $limit = null, int $page = 1): array
    {
        // Do not lose already set query parameters.
        $query_params = [];
        parse_str($uri->getQuery(), $query_params);
        $query_params += [
            'page' => $page,
        ];

        if (null !== $limit) {
            $query_params['size'] = $limit;
        }

        // This makes easier testing and also creates a query parameter list
        // that is easy to read.
        ksort($query_params);

        return $this->listEntities($uri->withQuery(http_build_query($query_params)));
    }
}
