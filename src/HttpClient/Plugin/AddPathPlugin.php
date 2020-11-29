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

namespace Apigee\Edge\HttpClient\Plugin;

use Apigee\Edge\Exception\InvalidArgumentException;
use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Forked and customized AddPathPlugin from php-http/client-common:2.x branch.
 *
 * There is a long-lasting bug in the php-http/client-common:1.x branch that
 * has not fixed yet and probably it won't be fixed anytime soon. This is the
 * reason why we forked the 2.x version of this plugin to this library.
 *
 * @see https://github.com/php-http/client-common/blob/2.0.0/src/Plugin/AddPathPlugin.php
 * @see https://github.com/php-http/client-common/issues/171
 * @see https://github.com/php-http/client-common/issues/141
 * @deprecated in 2.0.6, will be removed before 3.0.0. No longer needed.
 */
final class AddPathPlugin implements Plugin
{
    /**
     * The URI.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    private $uri;

    /**
     * AddPathPlugin constructor.
     *
     * @param \Psr\Http\Message\UriInterface $uri
     *   The URI.
     */
    public function __construct(UriInterface $uri)
    {
        @trigger_error(sprintf('The %s class is deprecated since version 2.0.6 and will be removed in 3.0.0. Use %s instead.', get_class($this), Plugin\AddPathPlugin::class), E_USER_DEPRECATED);
        if ('' === $uri->getPath()) {
            throw new InvalidArgumentException('URI path cannot be empty');
        }

        if ('/' === substr($uri->getPath(), -1)) {
            $uri = $uri->withPath(rtrim($uri->getPath(), '/'));
        }

        $this->uri = $uri;
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $prepend = $this->uri->getPath();
        $path = $request->getUri()->getPath();

        if (0 !== strpos($path, $prepend)) {
            $request = $request->withUri($request->getUri()->withPath($prepend . $path));
        }

        return $next($request);
    }
}
