<?php

namespace Apigee\Edge\HttpClient;

use Apigee\Edge\HttpClient\Plugin\ResponseHandlerPlugin;
use Apigee\Edge\HttpClient\Util\Builder;
use Apigee\Edge\HttpClient\Util\BuilderInterface;
use Apigee\Edge\HttpClient\Util\Journal;
use Http\Client\Common\Plugin\AddHostPlugin;
use Http\Client\Common\Plugin\AddPathPlugin;
use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\Plugin\DecoderPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\Plugin\HistoryPlugin;
use Http\Client\HttpClient;
use Http\Discovery\UriFactoryDiscovery;
use Http\Message\Authentication;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class Client.
 *
 * Default API client implementation for Apigee Edge.
 *
 * @package Apigee\Edge\HttpClient
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class Client implements ClientInterface
{
    protected const CLIENT_VERSION = '2.0.x-dev';

    private const ENTERPRISE_URL = 'https://api.enterprise.apigee.com';

    private const API_VERSION = 'v1';

    /** @var string */
    private $userAgentPrefix = '';

    /**
     * On-prem Apigee Endpoint endpoint.
     *
     * @var null|string
     */
    private $endpoint = '';

    /** @var Authentication */
    private $auth;

    /** @var BuilderInterface */
    private $builder;

    /** @var CacheItemPoolInterface */
    private $cachePool;

    /** @var array */
    private $cacheConfig = [];

    /** @var Journal */
    private $journal;

    /** @var bool */
    private $rebuild = true;

    /**
     * Client constructor.
     *
     * @param Authentication|null $auth
     * @param BuilderInterface|null $builder
     * @param string|null $endpoint
     */
    public function __construct(
        Authentication $auth = null,
        BuilderInterface $builder = null,
        string $endpoint = null
    )
    {
        $this->auth = $auth;
        $this->endpoint = $endpoint;
        $this->builder = $builder ?: new Builder();
    }

    /**
     * Sets or removes rebuild flag from the underlying HTTP client.
     *
     * @param bool $rebuild
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
            'Accept-Encoding' => 'gzip',
        ];
    }

    /**
     * Returns default plugins used by the underlying HTTP client.
     *
     * @return array
     */
    protected function getDefaultPlugins(): array
    {
        $plugins = [
            new AddHostPlugin($this->getBaseUri(), ['replace' => true]),
            new AddPathPlugin(UriFactoryDiscovery::find()->createUri(self::API_VERSION)),
            new HeaderDefaultsPlugin($this->getDefaultHeaders()),
            new HistoryPlugin($this->getJournal()),
            new DecoderPlugin(),
            new ResponseHandlerPlugin(),
        ];
        if ($this->auth) {
            $plugins[] = new AuthenticationPlugin($this->auth);
        }
        return $plugins;
    }

    /**
     * Returns Apigee Edge endpoint as am URI.
     *
     * @return UriInterface
     */
    protected function getBaseUri(): UriInterface
    {
        return UriFactoryDiscovery::find()->createUri($this->getEndpoint());
    }

    /**
     * @inheritdoc
     */
    public function addUserAgentPrefix(string $prefix): string
    {
        $this->needsRebuild(true);
        $this->userAgentPrefix = $prefix;
        return $this->userAgentPrefix;
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
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->getHttpClient()->sendRequest($request);
    }

    /**
     * @inheritdoc
     */
    public function getHttpClient(): HttpClient
    {
        return $this->getHttpClientBuilder()->getHttpClient();
    }

    /**
     * @inheritdoc
     */
    public function getHttpClientBuilder(): BuilderInterface
    {
        if ($this->rebuild()) {
            $this->needsRebuild(false);
            $this->builder->clearHeaders();
            $this->builder->clearPlugins();
            $this->builder->removeCache();
            foreach ($this->getDefaultPlugins() as $plugin) {
                $this->builder->addPlugin($plugin);
            }
            if ($this->cachePool) {
                $this->builder->addCache($this->cachePool, $this->cacheConfig);
            }
        }
        return $this->builder;
    }

    /**
     * @inheritdoc
     */
    public function getEndpoint(): string
    {
        return $this->endpoint ?: self::ENTERPRISE_URL;
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
        if ($this->userAgentPrefix) {
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
    public function getJournal(): Journal
    {
        if ($this->journal === null) {
            $this->journal = new Journal();
        }
        return $this->journal;
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
    public function send($method, $uri, array $headers = [], $body = null): ResponseInterface
    {
        return $this->sendRequest($this->getHttpClientBuilder()->getRequestFactory()->createRequest(
            $method,
            $uri,
            $headers,
            $body
        ));
    }
}
