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

namespace Apigee\Edge;

use Apigee\Edge\HttpClient\Utility\JournalInterface;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface ClientInterface.
 *
 * Describes the public methods of an Apigee Edge API client.
 */
interface ClientInterface extends HttpClient
{
    /**
     * Default endpoint for Apigee Edge Public Cloud.
     *
     * @var string
     *
     * @deprecated in 2.0.9, will be removed in 3.0.0. No longer needed.
     * https://github.com/apigee/apigee-client-php/issues/112
     */
    public const DEFAULT_ENDPOINT = 'https://api.enterprise.apigee.com/v1';

    /**
     * Default endpoint for Apigee Edge Public Cloud.
     *
     * @var string
     */
    public const EDGE_ENDPOINT = 'https://api.enterprise.apigee.com/v1';

    /**
     * Default endpoint for Apigee Edge Hybrid Cloud.
     *
     * @var string
     *
     * @deprecated in 2.0.9, will be removed in 3.0.0. No longer needed.
     * https://github.com/apigee/apigee-client-php/issues/112
     */
    public const HYBRID_ENDPOINT = 'https://apigee.googleapis.com/v1';

    /**
     * Default endpoint for Apigee Management API on GCP.
     *
     * @var string
     */
    public const APIGEE_ON_GCP_ENDPOINT = 'https://apigee.googleapis.com/v1';

    /**
     * Default endpoint for Apigee Management API on GCP DRZ United States location.
     *
     * @var string
     */
    public const APIGEE_ON_GCP_US_DRZ_ENDPOINT = 'https://us-apigee.googleapis.com/v1';

    /**
     * Default endpoint for Apigee Management API on GCP DRZ Canada location.
     *
     * @var string
     */
    public const APIGEE_ON_GCP_CA_DRZ_ENDPOINT = 'https://ca-apigee.googleapis.com/v1';

    /**
     * Default endpoint for Apigee Management API on GCP DRZ European Union location.
     *
     * @var string
     */
    public const APIGEE_ON_GCP_EU_DRZ_ENDPOINT = 'https://eu-apigee.googleapis.com/v1';

    /**
     * Default endpoint for Apigee Management API on GCP DRZ Germany location.
     *
     * @var string
     */
    public const APIGEE_ON_GCP_DE_DRZ_ENDPOINT = 'https://de-apigee.googleapis.com/v1';

    /**
     * Default endpoint for Apigee Management API on GCP DRZ France location.
     *
     * @var string
     */
    public const APIGEE_ON_GCP_FR_DRZ_ENDPOINT = 'https://fr-apigee.googleapis.com/v1';

    /**
     * Default endpoint for Apigee Management API on GCP DRZ Switzerland location.
     *
     * @var string
     */
    public const APIGEE_ON_GCP_CH_DRZ_ENDPOINT = 'https://ch-apigee.googleapis.com/v1';

    /**
     * Default endpoint for Apigee Management API on GCP DRZ Australia location.
     *
     * @var string
     */
    public const APIGEE_ON_GCP_AU_DRZ_ENDPOINT = 'https://au-apigee.googleapis.com/v1';

    /**
     * Default endpoint for Apigee Management API on GCP DRZ India location.
     *
     * @var string
     */
    public const APIGEE_ON_GCP_IN_DRZ_ENDPOINT = 'https://in-apigee.googleapis.com/v1';

    /**
     * Default endpoint for Apigee Management API on GCP DRZ Japan location.
     *
     * @var string
     */
    public const APIGEE_ON_GCP_JP_DRZ_ENDPOINT = 'https://jp-apigee.googleapis.com/v1';

    /**
     * Default endpoint for Apigee Management API on GCP DRZ Saudi Arabia location.
     *
     * @var string
     */
    public const APIGEE_ON_GCP_SA_DRZ_ENDPOINT = 'https://sa-apigee.googleapis.com/v1';

    /**
     * Default endpoint for Apigee Management API on GCP DRZ Israel location.
     *
     * @var string
     */
    public const APIGEE_ON_GCP_IL_DRZ_ENDPOINT = 'https://il-apigee.googleapis.com/v1';

    /**
     * A space-delimited list of the permissions that the application requests.
     *
     * @var string
     */
    public const APIGEE_TOKEN_ENDPOINT = 'https://www.googleapis.com/auth/cloud-platform';

    public const VERSION = '3.0.8';

    /**
     * Allows access to the last request, response and exception.
     *
     * @return JournalInterface
     */
    public function getJournal(): JournalInterface;

    /**
     * Returns the URI factory used by the Client.
     *
     * @return \Http\Message\UriFactory|\Psr\Http\Message\UriFactoryInterface
     *
     * @todo Restore native return type as \Psr\Http\Message\UriFactoryInterface in 4.0.0.
     */
    public function getUriFactory();

    /**
     * Returns the version of the API client.
     *
     * @return string
     */
    public function getClientVersion(): string;

    /**
     * Returns the user agent that the API client sends to Apigee Edge.
     *
     * @return string|null
     */
    public function getUserAgent(): ?string;

    /**
     * Returns the endpoint that the client currently communicates with.
     *
     * @return string
     */
    public function getEndpoint(): string;

    /**
     * Sends a GET request.
     *
     * @param \Psr\Http\Message\UriInterface|string $uri
     * @param array $headers
     *
     * @throws Exception\ApiException
     * @throws \Http\Client\Exception
     *
     * @return ResponseInterface
     */
    public function get($uri, array $headers = []): ResponseInterface;

    /**
     * Sends a HEAD request.
     *
     * @param \Psr\Http\Message\UriInterface|string $uri
     * @param array $headers
     *
     * @throws Exception\ApiException
     * @throws \Http\Client\Exception
     *
     * @return ResponseInterface
     */
    public function head($uri, array $headers = []): ResponseInterface;

    /**
     * Sends a POST request.
     *
     * @param \Psr\Http\Message\UriInterface|string $uri
     * @param \Psr\Http\Message\StreamInterface|resource|string|null $body
     * @param array $headers
     *
     * @throws Exception\ApiException
     * @throws \Http\Client\Exception
     *
     * @return ResponseInterface
     */
    public function post($uri, $body = null, array $headers = []): ResponseInterface;

    /**
     * Sends a PUT request.
     *
     * @param \Psr\Http\Message\UriInterface|string $uri
     * @param \Psr\Http\Message\StreamInterface|resource|string|null $body
     * @param array $headers
     *
     * @throws Exception\ApiException
     * @throws \Http\Client\Exception
     *
     * @return ResponseInterface
     */
    public function put($uri, $body = null, array $headers = []): ResponseInterface;

    /**
     * Sends a DELETE request.
     *
     * @param \Psr\Http\Message\UriInterface|string $uri
     * @param \Psr\Http\Message\StreamInterface|resource|string|null $body
     * @param array $headers
     *
     * @throws Exception\ApiException
     * @throws \Http\Client\Exception
     *
     * @return ResponseInterface
     */
    public function delete($uri, $body = null, array $headers = []): ResponseInterface;
}
