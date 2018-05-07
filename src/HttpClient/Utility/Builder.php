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

namespace Apigee\Edge\HttpClient\Utility;

use Http\Client\Common\Plugin;
use Http\Client\Common\Plugin\HeaderAppendPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\StreamFactoryDiscovery;
use Http\Message\RequestFactory;
use Http\Message\StreamFactory;

/**
 * Class Builder.
 *
 * Helper class that makes creation of HTTP client instances easier.
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

    /**
     * Http client plugins.
     *
     * @var \Http\Client\Common\Plugin[]
     */
    private $plugins = [];

    /** @var bool */
    private $rebuild = true;

    /**
     * Builder constructor.
     *
     * @param \Http\Client\HttpClient|null $httpClient
     * @param \Http\Message\RequestFactory|null $requestFactory
     * @param \Http\Message\StreamFactory|null $streamFactory
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
            $this->pluginClient = new PluginClient($this->httpClient, $this->plugins);
            $this->needsRebuild(false);
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
