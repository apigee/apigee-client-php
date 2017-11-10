<?php

namespace Apigee\Edge\HttpClient\Util;

use Http\Client\Common\Plugin;
use Http\Client\Common\Plugin\HeaderAppendPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\StreamFactoryDiscovery;
use Http\Discovery\UriFactoryDiscovery;
use Http\Message\RequestFactory;
use Http\Message\StreamFactory;
use Http\Message\UriFactory;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\InvalidArgumentException;

/**
 * Trait BuilderAwareTrait.
 *
 * @package Apigee\Edge\HttpClient\Util
 * @author Dezső Biczó <mxr576@gmail.com>
 * @see BuilderInterface
 */
trait BuilderAwareTrait
{
    /** @var HttpClient */
    private $httpClient;

    /** @var PluginClient */
    private $pluginClient;

    /** @var StreamFactory */
    private $streamFactory;

    /** @var RequestFactory */
    private $requestFactory;

    /** @var UriFactory */
    private $uriFactory;

    /** @var array */
    private $headers = [];

    /** @var array */
    private $plugins = [];

    /** @var Plugin\CachePlugin */
    private $cachePlugin;

    /** @var bool */
    private $rebuild = true;

    /**
     * @inheritdoc
     */
    public function getHttpClient(): HttpClient
    {
        if ($this->rebuild()) {
            $this->needsRebuild(true);

            if ($this->httpClient === null) {
                $this->httpClient = HttpClientDiscovery::find();
            }

            $plugins = $this->plugins;
            if ($this->cachePlugin) {
                $plugins[] = $this->cachePlugin;
            }

            $this->pluginClient = new PluginClient($this->httpClient, $plugins);
        }

        return $this->pluginClient;
    }

    /**
     * @inheritdoc
     */
    public function getStreamFactory(): StreamFactory
    {
        if ($this->streamFactory === null) {
            $this->streamFactory = StreamFactoryDiscovery::find();
        }

        return $this->streamFactory;
    }

    /**
     * @inheritdoc
     */
    public function getRequestFactory(): RequestFactory
    {
        if ($this->requestFactory === null) {
            $this->requestFactory = RequestFactory::find();
        }

        return $this->requestFactory;
    }

    /**
     * @inheritdoc
     */
    public function getUriFactory(): UriFactory
    {
        if ($this->uriFactory === null) {
            $this->uriFactory = UriFactoryDiscovery::find();
        }

        return $this->uriFactory;
    }

    /**
     * Set or remove rebuild flag from the client.
     *
     * @param bool $rebuild
     * @return bool
     */
    private function needsRebuild(bool $rebuild = true): bool
    {
        return $this->rebuild = $rebuild;
    }

    /**
     * Indicates whether the client has to be rebuilt before a new request after a latest change.
     *
     * @return bool
     */
    private function rebuild(): bool
    {
        return $this->rebuild;
    }

    /**
     * @inheritdoc
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = array_merge($this->headers, $headers);
        $this->removePlugin(HeaderAppendPlugin::class);
        $this->addPlugin(new HeaderAppendPlugin($this->headers));
    }

    /**
     * @inheritdoc
     */
    public function clearHeaders(): void
    {
        $this->headers = [];
        $this->removePlugin(HeaderAppendPlugin::class);
    }

    /**
     * @inheritdoc
     */
    public function setHeaderValue(string $header, string $value): void
    {
        if (!isset($this->headers[$header])) {
            $this->headers[$header] = $value;
        } else {
            $this->headers[$header] = array_merge((array)$this->headers[$header], [$value]);
        }

        $this->removePlugin(HeaderAppendPlugin::class);
        $this->addPlugin(new HeaderAppendPlugin($this->headers));
    }

    /**
     * @inheritdoc
     */
    public function removeHeader(string $header): void
    {
        if (isset($this->headers[$header])) {
            unset($this->headers[$header]);
            $this->removePlugin(HeaderAppendPlugin::class);
            $this->addPlugin(new HeaderAppendPlugin($this->headers));
        }
    }

    /**
     * @inheritdoc
     */
    public function addPlugin(Plugin $plugin): void
    {
        if ($plugin instanceof Plugin\CachePlugin) {
            throw new InvalidArgumentException('Cache plugins should be added with addCache() method.');
        }
        $this->needsRebuild(true);
        $this->plugins[] = $plugin;
    }

    /**
     * @inheritdoc
     */
    public function removePlugin(string $fqcn): void
    {
        foreach ($this->plugins as $id => $plugin) {
            if ($plugin instanceof $fqcn) {
                unset($this->plugins[$id]);
                $this->needsRebuild(true);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function clearPlugins(): void
    {
        $this->plugins = [];
        $this->needsRebuild(true);
    }

    /**
     * @inheritdoc
     */
    public function addCache(CacheItemPoolInterface $cache, array $config = []): void
    {
        $this->cachePlugin = Plugin\CachePlugin::clientCache($cache, $this->getStreamFactory(), $config);
        $this->needsRebuild(true);
    }

    /**
     * @inheritdoc
     */
    public function removeCache(): void
    {
        $this->cachePlugin = null;
        $this->needsRebuild(true);
    }
}
