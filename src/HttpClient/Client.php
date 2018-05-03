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

namespace Apigee\Edge\HttpClient;

use Apigee\Edge\HttpClient\Plugin\Authentication\Oauth;
use Apigee\Edge\HttpClient\Plugin\ResponseHandlerPlugin;
use Apigee\Edge\HttpClient\Plugin\RetryOauthAuthenticationPlugin;
use Apigee\Edge\HttpClient\Utility\Builder;
use Apigee\Edge\HttpClient\Utility\BuilderInterface;
use Apigee\Edge\HttpClient\Utility\Journal;
use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\Plugin\BaseUriPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\Plugin\HistoryPlugin;
use Http\Client\HttpClient;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\UriFactoryDiscovery;
use Http\Message\Authentication;
use Http\Message\Formatter;
use Http\Message\UriFactory;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class Client.
 *
 * Default API client implementation for Apigee Edge.
 */
class Client implements ClientInterface
{
    protected const CLIENT_VERSION = '2.0.x-dev';

    private const ENTERPRISE_URL = 'https://api.enterprise.apigee.com';

    private const API_VERSION = 'v1';

    /** @var \Http\Message\UriFactory */
    protected $uriFactory;

    /** @var string|null */
    private $userAgentPrefix;

    /**
     * On-prem Apigee Endpoint endpoint.
     *
     * @var string
     */
    private $endpoint;

    /** @var \Http\Message\Authentication|null */
    private $auth;

    /**
     * Stores the originally passed builder in case of rebuild.
     *
     * @var \Apigee\Edge\HttpClient\Utility\BuilderInterface
     */
    private $originalBuilder;

    /**
     * Stores the current, altered builder instance.
     *
     * @var \Apigee\Edge\HttpClient\Utility\BuilderInterface
     */
    private $currentBuilder;

    /** @var \Psr\Cache\CacheItemPoolInterface|null */
    private $cachePool;

    /** @var array */
    private $cacheConfig = [];

    /** @var \Apigee\Edge\HttpClient\Utility\Journal */
    private $journal;

    /** @var bool */
    private $rebuild = true;

    /**
     * @var \Http\Message\RequestFactory
     */
    private $requestFactory;

    /**
     * @var \Http\Message\Formatter|null
     */
    private $errorFormatter;

    /**
     * Client constructor.
     *
     * @param \Http\Message\Authentication|null $auth
     *   Authentication plugin.
     * @param \Apigee\Edge\HttpClient\Utility\BuilderInterface|null $builder
     *   Http client builder.
     * @param string|null $endpoint
     *   The Apigee Edge API endpoint, including API version. Ex.: https://api.enterprise.apigee.com/v1 (which is the
     *   default value).
     * @param string|null $userAgentPrefix
     *   User agent prefix.
     * @param \Http\Message\Formatter|null $errorFormatter
     *   (For response handler plugin) Formats API communication errors.
     */
    public function __construct(
        Authentication $auth = null,
        BuilderInterface $builder = null,
        string $endpoint = null,
        string $userAgentPrefix = null,
        Formatter $errorFormatter = null
    ) {
        $this->auth = $auth;
        $this->currentBuilder = $builder ?: new Builder();
        $this->originalBuilder = $this->currentBuilder;
        $this->endpoint = $endpoint ?: self::ENTERPRISE_URL . '/' . self::API_VERSION;
        $this->userAgentPrefix = $userAgentPrefix;
        $this->uriFactory = UriFactoryDiscovery::find();
        $this->requestFactory = MessageFactoryDiscovery::find();
        $this->journal = new Journal();
        $this->errorFormatter = $errorFormatter;
    }

    /**
     * @inheritdoc
     */
    public function getJournal(): Journal
    {
        return $this->journal;
    }

    /**
     * @inheritdoc
     */
    public function setUserAgentPrefix(?string $prefix): void
    {
        $this->needsRebuild(true);
        $this->userAgentPrefix = $prefix;
    }

    /**
     * @inheritdoc
     */
    public function addCache(CacheItemPoolInterface $cachePool, array $config = []): void
    {
        $this->needsRebuild(true);
        $this->cachePool = $cachePool;
        $this->cacheConfig = $config;
    }

    /**
     * @inheritdoc
     */
    public function removeCache(): void
    {
        $this->needsRebuild(true);
        $this->cachePool = null;
        $this->cacheConfig = [];
    }

    /**
     * @inheritdoc
     */
    public function getUriFactory(): UriFactory
    {
        return $this->uriFactory;
    }

    /**
     * @inheritdoc
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @inheritdoc
     */
    public function setEndpoint(string $endpoint): string
    {
        $this->needsRebuild(true);
        $this->endpoint = $endpoint;

        return $this->endpoint;
    }

    /**
     * @inheritdoc
     */
    public function getUserAgent(): string
    {
        if (null !== $this->userAgentPrefix) {
            return sprintf('%s (%s)', $this->userAgentPrefix, $this->getClientVersion());
        }

        return $this->getClientVersion();
    }

    /**
     * @inheritdoc
     */
    public function getClientVersion(): string
    {
        return sprintf('Apigee Edge PHP SDK %s', self::CLIENT_VERSION);
    }

    /**
     * @inheritdoc
     */
    public function get($uri, array $headers = []): ResponseInterface
    {
        return $this->send('GET', $uri, $headers, null);
    }

    /**
     * @inheritdoc
     */
    public function head($uri, array $headers = []): ResponseInterface
    {
        return $this->send('HEAD', $uri, $headers, null);
    }

    /**
     * @inheritdoc
     */
    public function post($uri, $body = null, array $headers = []): ResponseInterface
    {
        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type'] = 'application/json; charset=utf-8';
        }

        return $this->send('POST', $uri, $headers, $body);
    }

    /**
     * @inheritdoc
     */
    public function put($uri, $body = null, array $headers = []): ResponseInterface
    {
        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type'] = 'application/json; charset=utf-8';
        }

        return $this->send('PUT', $uri, $headers, $body);
    }

    /**
     * @inheritdoc
     */
    public function delete($uri, $body = null, array $headers = []): ResponseInterface
    {
        return $this->send('DELETE', $uri, $headers, $body);
    }

    /**
     * @inheritdoc
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->getHttpClient()->sendRequest($request);
    }

    /**
     * Returns default HTTP headers sent by the underlying HTTP client.
     *
     * @return array
     */
    protected function getDefaultHeaders(): array
    {
        return [
            'User-Agent' => $this->getUserAgent(),
            'Accept' => 'application/json; charset=utf-8',
        ];
    }

    /**
     * Returns default plugins used by the underlying HTTP client.
     *
     * Call order of default plugins for sending a request (only those plugins listed that actually does something):
     * Request -> PluginClient -> BaseUriPlugin -> HeaderDefaultsPlugin -> HttpClient
     *
     * Call order of default plugins for processing a response (only those plugins listed that actually does something):
     * HttpClient -> ResponseHandlerPlugin -> RetryOauthAuthenticationPlugin -> HistoryPlugin -> Response
     *
     * @return \Http\Client\Common\Plugin[]
     */
    protected function getDefaultPlugins(): array
    {
        // Alters requests, adds base path and authentication.
        $firstPlugins = [
            new BaseUriPlugin($this->getBaseUri(), ['replace' => true]),
            new HeaderDefaultsPlugin($this->getDefaultHeaders()),
        ];

        if ($this->auth) {
            $firstPlugins[] = new AuthenticationPlugin($this->auth);
        }

        // Acts based on response data.
        // (Retry plugin should be added here if it will be used.)
        $middlePlugins = [
            new HistoryPlugin($this->journal),
        ];

        if ($this->auth instanceof Oauth) {
            $middlePlugins[] = new RetryOauthAuthenticationPlugin($this->auth);
        }

        // Alters, analyze responses.
        $finalPlugins = [
            new ResponseHandlerPlugin($this->errorFormatter),
        ];

        return array_merge($firstPlugins, $middlePlugins, $finalPlugins);
    }

    /**
     * @inheritdoc
     */
    private function send($method, $uri, array $headers = [], $body = null): ResponseInterface
    {
        return $this->sendRequest($this->requestFactory->createRequest($method, $uri, $headers, $body));
    }

    /**
     * Returns Apigee Edge endpoint as an URI.
     *
     * @return UriInterface
     */
    private function getBaseUri(): UriInterface
    {
        return $this->uriFactory->createUri($this->getEndpoint());
    }

    /**
     * @inheritdoc
     */
    private function getHttpClientBuilder(): BuilderInterface
    {
        if ($this->rebuild()) {
            $this->needsRebuild(false);
            $this->currentBuilder = clone $this->originalBuilder;
            foreach ($this->getDefaultPlugins() as $plugin) {
                $this->currentBuilder->addPlugin($plugin);
            }
            if ($this->cachePool) {
                $this->currentBuilder->addCache($this->cachePool, $this->cacheConfig);
            }
        }

        return $this->currentBuilder;
    }

    /**
     * @inheritdoc
     */
    private function getHttpClient(): HttpClient
    {
        return $this->getHttpClientBuilder()->getHttpClient();
    }

    /**
     * Sets or removes rebuild flag from the underlying HTTP client.
     *
     * @param bool $rebuild
     *
     * @return bool
     */
    private function needsRebuild(bool $rebuild = true): bool
    {
        return $this->rebuild = $rebuild;
    }

    /**
     * Indicates whether the underlying HTTP clients needs to be rebuilt before the next API call.
     *
     * @return bool
     */
    private function rebuild(): bool
    {
        return $this->rebuild;
    }
}
