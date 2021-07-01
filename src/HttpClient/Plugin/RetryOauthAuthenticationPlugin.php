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

use Apigee\Edge\Exception\OauthAccessTokenAuthenticationException;
use Apigee\Edge\HttpClient\Plugin\Authentication\AbstractOauth;
use Http\Client\Common\Plugin;
use Http\Client\Exception;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Automatically re-authenticate if a request fails because of an expired access- or refresh token.
 */
class RetryOauthAuthenticationPlugin implements Plugin
{
    /**
     * @var \Apigee\Edge\HttpClient\Plugin\Authentication\AbstractOauth
     */
    private $auth;

    /**
     * RetryOauthAuthenticationPlugin constructor.
     *
     * @param \Apigee\Edge\HttpClient\Plugin\Authentication\AbstractOauth $auth
     */
    public function __construct(AbstractOauth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * {@inheritdoc}
     *
     * @see AbstractOauth::getAccessToken()
     *
     * @psalm-suppress InvalidThrow - Exception with interface can be thrown.
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        return $next($request)->then(function (ResponseInterface $response) {
            return $response;
        }, function (Exception $exception) use ($request, $first) {
            if ($exception instanceof OauthAccessTokenAuthenticationException) {
                // Mark access token as expired and with that ensure that the authentication plugin gets a new
                // access token.
                $this->auth->getTokenStorage()->markExpired();
                $promise = $first($request);

                return $promise->wait();
            }

            throw $exception;
        });
    }
}
