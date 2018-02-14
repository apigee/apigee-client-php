<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\HttpClient\Plugin;

use Apigee\Edge\Exception\ClientErrorException;
use Apigee\Edge\Exception\ServerErrorException;
use Http\Client\Common\Plugin;
use Http\Message\Formatter;
use Http\Message\Formatter\FullHttpMessageFormatter;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ResponseHandlerPlugin.
 */
final class ResponseHandlerPlugin implements Plugin
{
    /** @var \Http\Message\Formatter */
    private $formatter;

    /**
     * ResponseHandlerPlugin constructor.
     *
     * @param \Http\Message\Formatter|null $formatter
     */
    public function __construct(Formatter $formatter = null)
    {
        $this->formatter = $formatter ?: new FullHttpMessageFormatter();
    }

    /**
     * @inheritdoc
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first)
    {
        return $next($request)->then(function (ResponseInterface $response) use ($request) {
            return $this->decodeResponse($response, $request);
        });
    }

    /**
     * @param ResponseInterface $response
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    private function decodeResponse(ResponseInterface $response, RequestInterface $request)
    {
        if ($response->getStatusCode() >= 400 && $response->getStatusCode() < 500) {
            throw new ClientErrorException($response, $request, (string) $response->getBody(), $response->getStatusCode(), null, $this->formatter);
        } elseif ($response->getStatusCode() >= 500 && $response->getStatusCode() < 600) {
            throw new ServerErrorException($response, $request, (string) $response->getBody(), $response->getStatusCode(), null, $this->formatter);
        }

        return $response;
    }
}
