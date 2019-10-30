<?php

/*
 * Copyright 2019 Google LLC
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

namespace Apigee\Edge\HttpClient\Plugin\Authentication;

use Apigee\Edge\ClientInterface;
use Apigee\Edge\Utility\DeprecatedPropertyTrait;
use Http\Message\Authentication;
use Http\Message\Authentication\Bearer;
use Psr\Http\Message\RequestInterface;

/**
 * Abstract Oauth base authentication plugin.
 */
abstract class AbstractOauth implements Authentication
{
    use DeprecatedPropertyTrait;

    /**
     * @var \Apigee\Edge\HttpClient\Plugin\Authentication\OauthTokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var string
     */
    protected $authServer;

    /**
     * @inheritdoc
     */
    protected $deprecatedProperties = ['auth_server' => 'authServer'];

    /**
     * Constructor.
     *
     * @param \Apigee\Edge\HttpClient\Plugin\Authentication\OauthTokenStorageInterface $token_storage
     *   Storage where access token gets saved.
     * @param string $authServer
     *   Authentication server.
     */
    public function __construct(OauthTokenStorageInterface $token_storage, string $authServer)
    {
        $this->tokenStorage = $token_storage;
        $this->authServer = $authServer;
    }

    /**
     * @inheritdoc
     */
    public function authenticate(RequestInterface $request)
    {
        // Get a new access token if token has expired.
        if ($this->tokenStorage->hasExpired()) {
            $this->getAccessToken();
        }

        $accessToken = $this->tokenStorage->getAccessToken();

        if (!empty($accessToken)) {
            $bearAuth = new Bearer($accessToken);
            $request = $bearAuth->authenticate($request);
        }

        return $request;
    }

    /**
     * Returns the token storage.
     *
     * @return \Apigee\Edge\HttpClient\Plugin\Authentication\OauthTokenStorageInterface
     */
    public function getTokenStorage(): OauthTokenStorageInterface
    {
        return $this->tokenStorage;
    }

    /**
     * Returns a pre-configured client for authorization API calls.
     *
     * @return \Apigee\Edge\ClientInterface
     */
    abstract protected function authClient(): ClientInterface;

    /**
     * Retrieves access token and saves it to the token storage.
     */
    abstract protected function getAccessToken(): void;
}
