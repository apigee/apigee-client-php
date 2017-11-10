<?php

namespace Apigee\Edge\HttpClient\Util;

use Http\Client\Common\Plugin;
use Http\Client\HttpClient;
use Http\Message\RequestFactory;
use Http\Message\StreamFactory;
use Http\Message\UriFactory;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Interface BuilderInterface.
 *
 * Describes the public methods of a class that helps in building an Http client.
 *
 * @package Apigee\Edge\HttpClient\Util
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface BuilderInterface
{
    /**
     * @return HttpClient
     */
    public function getHttpClient(): HttpClient;

    /**
     * @return StreamFactory
     */
    public function getStreamFactory(): StreamFactory;

    /**
     * @return RequestFactory
     */
    public function getRequestFactory(): RequestFactory;

    /**
     * @return UriFactory
     */
    public function getUriFactory(): UriFactory;

    /**
     * @param array $headers Associate array of HTTP headers.
     */
    public function setHeaders(array $headers): void;

    /**
     * Clear previously set HTTP headers.
     */
    public function clearHeaders(): void;

    /**
     * Add/change header value.
     *
     * @param string $header Header name.
     * @param string $value Header value
     */
    public function setHeaderValue(string $header, string $value): void;

    /**
     * @param string $header Header name.
     */
    public function removeHeader(string $header): void;

    /**
     * Add plugin to the client.
     *
     * @param Plugin $plugin
     * @return mixed
     */
    public function addPlugin(Plugin $plugin): void;

    /**
     * @param string $fqcn Fully qualified class name of the plugin.
     * @return mixed
     */
    public function removePlugin(string $fqcn): void;

    /**
     * Remove all previously added  plugins from the client.
     */
    public function clearPlugins(): void;

    /**
     * Add cache to the client.
     *
     * @param CacheItemPoolInterface $cachePool
     * @param array $config
     */
    public function addCache(CacheItemPoolInterface $cachePool, array $config = []): void;

    /**
     * Remove cache from the client.
     */
    public function removeCache(): void;
}
