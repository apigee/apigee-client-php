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
use Apigee\Edge\Exception\OauthAuthenticationException;
use Apigee\Edge\Exception\OauthRefreshTokenExpiredException;
use Apigee\Edge\HttpClient\Plugin\Authentication\Oauth;
use Http\Client\Common\Plugin;
use Http\Client\Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Automatically re-authenticate if a request fails because of an expired access- or refresh token.
 */
class RetryOauthAuthenticationPlugin implements Plugin
{
    /**
     * @var \Apigee\Edge\HttpClient\Plugin\Authentication\Oauth
     */
    private $auth;

    /**
     * RetryOauthAuthenticationPlugin constructor.
     *
     * @param \Apigee\Edge\HttpClient\Plugin\Authentication\Oauth $auth
     */
    public function __construct(Oauth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @inheritdoc
     *
     * @see Oauth::getAccessToken()
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first)
    {
        return $next($request)->then(function (ResponseInterface $response) {
            return $response;
        }, function (Exception $exception) use ($request, $next, $first) {
            if ($exception instanceof OauthAccessTokenAuthenticationException) {
                // Mark access token as expired and with that ensure that the authentication plugin gets a new
                // access token.
                $this->auth->getTokenStorage()->markExpired();
                try {
                    $promise = $first($request);
                } catch (OauthAuthenticationException $e) {
                    if ($e->getPrevious() instanceof OauthRefreshTokenExpiredException) {
                        // Clear token date from the storage and with that ensure before the retry plugin resends
                        // this failed request the client tries to get a new access token first by using the resource
                        // owner username as password as credentials.
                        $this->auth->getTokenStorage()->removeToken();
                        $promise = $first($request);
                    } else {
                        throw $e;
                    }
                }

                return $promise->wait();
            }
        });
    }
}
