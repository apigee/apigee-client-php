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
use Psr\Http\Client\ClientInterface;

/**
 * Interface BuilderInterface.
 *
 * Describes the public methods of a class that helps in building an Http client.
 */
interface BuilderInterface
{
    /**
     * @return \Psr\Http\Client\ClientInterface
     */
    public function getHttpClient(): ClientInterface;

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
     * Adds a plugin to the http client.
     *
     * The plugin is added to the end of the plugin list.
     *
     * @param \Http\Client\Common\Plugin
     *   Http client plugin.
     *
     * @see http://docs.php-http.org/en/latest/plugins/introduction.html#how-it-works
     */
    public function addPlugin(Plugin $plugin): void;

    /**
     * @param string $fqcn Fully qualified class name of the http client plugin.
     */
    public function removePlugin(string $fqcn): void;

    /**
     * Remove all previously added  plugins from the client.
     */
    public function clearPlugins(): void;
}
