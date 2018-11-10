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

use Apigee\Edge\Exception\ApiResponseException;
use Apigee\Edge\Exception\OauthAuthenticationException;
use Apigee\Edge\HttpClient\Plugin\Authentication\Oauth;
use Apigee\Edge\HttpClient\Plugin\ResponseHandlerPlugin;
use Apigee\Edge\HttpClient\Plugin\RetryOauthAuthenticationPlugin;
use Apigee\Edge\HttpClient\Utility\Builder;
use Apigee\Edge\HttpClient\Utility\Journal;
use Apigee\Edge\HttpClient\Utility\JournalInterface;
use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\Plugin\BaseUriPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\Plugin\HistoryPlugin;
use Http\Client\Common\Plugin\RetryPlugin;
use Http\Client\Exception;
use Http\Client\HttpClient;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\UriFactoryDiscovery;
use Http\Message\Authentication;
use Http\Message\UriFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Client.
 *
 * Default API client implementation for Apigee Edge.
 */
class Client implements ClientInterface
{
    public const CONFIG_USER_AGENT_PREFIX = 'user_agent_prefix';

    public const CONFIG_HTTP_CLIENT_BUILDER = 'http_client_builder';

    public const CONFIG_URI_FACTORY = 'uri_factory';

    public const CONFIG_REQUEST_FACTORY = 'request_factory';

    public const CONFIG_JOURNAL = 'journal';

    public const CONFIG_ERROR_FORMATTER = 'error_formatter';

    public const CONFIG_RETRY_PLUGIN_CONFIG = 'retry_plugin_config';

    /** @var \Http\Message\UriFactory */
    private $uriFactory;

    /** @var string|null */
    private $userAgentPrefix;

    /**
     * Apigee Edge endpoint.
     *
     * @var string
     */
    private $endpoint;

    /** @var \Http\Message\Authentication */
    private $authentication;

    /**
     * Http client builder.
     *
     * @var \Apigee\Edge\HttpClient\Utility\BuilderInterface
     */
    private $httpClientBuilder;

    /** @var \Apigee\Edge\HttpClient\Utility\JournalInterface */
    private $journal;

    /** @var bool */
    private $httpClientNeedsBuild = true;

    /**
     * @var \Http\Message\RequestFactory
     */
    private $requestFactory;

    /**
     * @var \Http\Message\Formatter|null
     */
    private $errorFormatter;

    /** @var null|array */
    private $retryPluginConfig;

    /**
     * Client constructor.
     *
     * @param \Http\Message\Authentication $authentication
     *   Authentication plugin.
     * @param string|null $endpoint
     *   The Apigee Edge API endpoint, including API version. Ex.: https://api.enterprise.apigee.com/v1 (which is the
     *   default value).
     * @param array $options
     *   Additional configurations for the client. Possible options:
     *   - Apigee\Edge\Client::CONFIG_USER_AGENT_PREFIX: null|string
     *     User agent prefix.
     *   - Apigee\Edge\Client::CONFIG_HTTP_CLIENT_BUILDER: \Apigee\Edge\HttpClient\Utility\BuilderInterface|null
     *     Http client builder.
     *   - Apigee\Edge\Client::CONFIG_URI_FACTORY: \Http\Message\UriFactory|null
     *     Factory for PSR-7 URIs.
     *   - Apigee\Edge\Client::CONFIG_REQUEST_FACTORY: \Http\Message\RequestFactory|null
     *     Factory for PSR-7 Requests.
     *   - Apigee\Edge\Client::CONFIG_JOURNAL: \Apigee\Edge\HttpClient\Utility\JournalInterface|null
     *     Records and returns history of HTTP calls.
     *   - Apigee\Edge\Client::CONFIG_ERROR_FORMATTER: \Http\Message\Formatter|null
     *     Formats requests and responses in exceptions generated by the response handler plugin.
     *   - Apigee\Edge\Client::CONFIG_RETRY_PLUGIN_CONFIG: array
     *     Retry plugin configuration. http://docs.php-http.org/en/latest/plugins/retry.html
     *     Set it to an empty array to use the default plugin configuration.
     */
    public function __construct(
        Authentication $authentication,
        string $endpoint = null,
        array $options = []
    ) {
        $this->authentication = $authentication;
        $this->endpoint = $endpoint ?: self::DEFAULT_ENDPOINT;
        $this->resolveConfiguration($options);
    }

    /**
     * @inheritdoc
     */
    public function getJournal(): JournalInterface
    {
        return $this->journal;
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
    public function getUserAgent(): string
    {
        if (null !== $this->userAgentPrefix) {
            return sprintf("{$this->userAgentPrefix} ({$this->getClientVersion()})");
        }

        return $this->getClientVersion();
    }

    /**
     * @inheritdoc
     */
    public function getClientVersion(): string
    {
        return sprintf('Apigee Edge PHP Client %s', self::VERSION);
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
    public function patch($uri, $body = null, array $headers = []): ResponseInterface
    {
        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type'] = 'application/json; charset=utf-8';
        }

        return $this->send('PATCH', $uri, $headers, $body);
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
     * Sets default for supported configuration options.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *   Option resolver.
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        // We set object properties' _default_ values to null to ensure we do not create unnecessary objects.
        $resolver->setDefaults([
            static::CONFIG_USER_AGENT_PREFIX => null,
            static::CONFIG_HTTP_CLIENT_BUILDER => null,
            static::CONFIG_JOURNAL => null,
            static::CONFIG_URI_FACTORY => null,
            static::CONFIG_REQUEST_FACTORY => null,
            static::CONFIG_ERROR_FORMATTER => null,
            static::CONFIG_RETRY_PLUGIN_CONFIG => null,
        ]);
        $resolver->setAllowedTypes(static::CONFIG_USER_AGENT_PREFIX, ['null', 'string']);
        $resolver->setAllowedTypes(static::CONFIG_HTTP_CLIENT_BUILDER, ['null', '\Apigee\Edge\HttpClient\Utility\BuilderInterface']);
        $resolver->setAllowedTypes(static::CONFIG_JOURNAL, ['null', '\Apigee\Edge\HttpClient\Utility\JournalInterface']);
        $resolver->setAllowedTypes(static::CONFIG_URI_FACTORY, ['null', '\Http\Message\UriFactory']);
        $resolver->setAllowedTypes(static::CONFIG_REQUEST_FACTORY, ['null', '\Http\Message\RequestFactory']);
        $resolver->setAllowedTypes(static::CONFIG_ERROR_FORMATTER, ['null', '\Http\Message\Formatter']);
        $resolver->setAllowedTypes(static::CONFIG_RETRY_PLUGIN_CONFIG, ['null', 'array']);
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

        if ($this->authentication) {
            $firstPlugins[] = new AuthenticationPlugin($this->authentication);
        }

        // Acts based on response data.
        // (Retry plugin should be added here if it will be used.)
        $middlePlugins = [
            new HistoryPlugin($this->journal),
        ];

        if (null !== $this->retryPluginConfig) {
            if (!isset($this->retryPluginConfig['decider'])) {
                $this->retryPluginConfig['decider'] = function (RequestInterface $request, Exception $e) {
                    // When Oauth authentication is in use retry decider should ignore
                    // OauthAuthenticationException.
                    if (!$e instanceof OauthAuthenticationException) {
                        // Do not retry API calls that failed with
                        // client error.
                        if ($e instanceof ApiResponseException && $e->getResponse()->getStatusCode() >= 400 && $e->getResponse()->getStatusCode() < 500) {
                            return false;
                        }

                        return true;
                    }

                    return false;
                };
            }
            $middlePlugins[] = new RetryPlugin($this->retryPluginConfig);
        }

        if ($this->authentication instanceof Oauth) {
            $middlePlugins[] = new RetryOauthAuthenticationPlugin($this->authentication);
        }

        // Alters, analyzes responses.
        $finalPlugins = [
            new ResponseHandlerPlugin($this->errorFormatter),
        ];

        return array_merge($firstPlugins, $middlePlugins, $finalPlugins);
    }

    /**
     * Resolve configuration options.
     *
     * @param array $options
     *   Array of configuration options.
     */
    private function resolveConfiguration(array $options = []): void
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $options = $resolver->resolve($options);
        $this->userAgentPrefix = $options[static::CONFIG_USER_AGENT_PREFIX];
        $this->httpClientBuilder = $options[static::CONFIG_HTTP_CLIENT_BUILDER] ?: new Builder();
        $this->uriFactory = $options[static::CONFIG_URI_FACTORY] ?: UriFactoryDiscovery::find();
        $this->requestFactory = $options[static::CONFIG_REQUEST_FACTORY] ?: MessageFactoryDiscovery::find();
        $this->journal = $options[static::CONFIG_JOURNAL] ?: new Journal();
        $this->errorFormatter = $options[static::CONFIG_ERROR_FORMATTER];
        $this->retryPluginConfig = $options[static::CONFIG_RETRY_PLUGIN_CONFIG];
    }

    /**
     * @inheritdoc
     *
     * @throws \Http\Client\Exception
     */
    private function send($method, $uri, array $headers = [], $body = null): ResponseInterface
    {
        return $this->sendRequest($this->requestFactory->createRequest($method, $uri, $headers, $body));
    }

    /**
     * Returns Apigee Edge endpoint as an URI.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    private function getBaseUri(): UriInterface
    {
        return $this->uriFactory->createUri($this->getEndpoint());
    }

    /**
     * @inheritdoc
     */
    private function getHttpClient(): HttpClient
    {
        if ($this->httpClientNeedsBuild) {
            foreach ($this->getDefaultPlugins() as $plugin) {
                $this->httpClientBuilder->addPlugin($plugin);
            }
            $this->httpClientNeedsBuild = false;
        }

        return $this->httpClientBuilder->getHttpClient();
    }
}
