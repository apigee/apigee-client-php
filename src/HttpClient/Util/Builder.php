<?php

namespace Apigee\Edge\HttpClient\Util;

use Http\Client\Common\Plugin;
use Http\Client\Common\Plugin\HeaderAppendPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\StreamFactoryDiscovery;
use Http\Message\RequestFactory;
use Http\Message\StreamFactory;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class Builder.
 *
 * Helper class that makes creation of HTTP client instances easier.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class Builder implements BuilderInterface
{
    /** @var HttpClient */
    private $httpClient;

    /** @var PluginClient */
    private $pluginClient;

    /** @var StreamFactory */
    private $streamFactory;

    /** @var RequestFactory */
    private $requestFactory;

    /** @var array */
    private $headers = [];

    /** @var array */
    private $plugins = [];

    /** @var Plugin\CachePlugin|null */
    private $cachePlugin;

    /** @var bool */
    private $rebuild = true;

    /**
     * Builder constructor.
     *
     * @param HttpClient|null $httpClient
     * @param RequestFactory|null $requestFactory
     * @param StreamFactory|null $streamFactory
     */
    public function __construct(
        HttpClient $httpClient = null,
        RequestFactory $requestFactory = null,
        StreamFactory $streamFactory = null
    ) {
        $this->httpClient = $httpClient ?: HttpClientDiscovery::find();
        $this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
        $this->streamFactory = $streamFactory ?: StreamFactoryDiscovery::find();
    }

    /**
     * @inheritdoc
     */
    public function getHttpClient(): HttpClient
    {
        if ($this->rebuild()) {
            $this->needsRebuild(true);

            if (null === $this->httpClient) {
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
            $this->headers[$header] = array_merge((array) $this->headers[$header], [$value]);
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
            throw new \InvalidArgumentException('Cache plugins should be added with addCache() method.');
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
        $this->cachePlugin = Plugin\CachePlugin::clientCache($cache, $this->streamFactory, $config);
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

    /**
     * Set or remove rebuild flag from the client.
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
     * Indicates whether the client has to be rebuilt before a new request after a latest change.
     *
     * @return bool
     */
    private function rebuild(): bool
    {
        return $this->rebuild;
    }
}
