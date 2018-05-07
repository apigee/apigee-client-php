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

namespace Apigee\Edge\HttpClient\Plugin\Authentication;

use Apigee\Edge\Client;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Exception\OauthAuthenticationException;
use Http\Client\Exception;
use Http\Message\Authentication;
use Http\Message\Authentication\BasicAuth;
use Http\Message\Authentication\Bearer;
use Psr\Http\Message\RequestInterface;

/**
 * Oauth authentication plugin for authenticating to Apigee Edge with Oauth (SAML).
 *
 * @see https://apidocs.apigee.com/api-reference/content/using-oauth2-security-apigee-edge-management-api
 */
class Oauth implements Authentication
{
    private const DEFAULT_AUTHORIZATION_SERVER = 'https://login.apigee.com/oauth/token';

    private const DEFAULT_CLIENT_ID = 'edgecli';

    private const DEFAULT_CLIENT_SECRET = 'edgeclisecret';

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var OauthTokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var string|null
     */
    private $mfaToken;

    /**
     * @var string
     */
    private $auth_server;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var string|null
     */
    private $scope;

    /**
     * Oauth constructor.
     *
     * @param string $username
     *   Apigee Edge username (email).
     * @param string $password
     *   Apigee Edge password.
     * @param \Apigee\Edge\HttpClient\Plugin\Authentication\OauthTokenStorageInterface $token_storage
     *   Storage where access token gets saved.
     * @param string|null $mfa_token
     *   One-time multi-factor authentication code.
     * @param string|null $client_id
     *   Client id.
     * @param string|null $client_secret
     *   Client secret.
     * @param string|null $scope
     *   Oauth scope.
     * @param string|null $auth_server
     *   Authentication server.
     */
    public function __construct(string $username, string $password, OauthTokenStorageInterface $token_storage, ?string $mfa_token = null, ?string $client_id = null, ?string $client_secret = null, ?string $scope = null, ?string $auth_server = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->tokenStorage = $token_storage;
        $this->mfaToken = $mfa_token;
        $this->auth_server = $auth_server ?: self::DEFAULT_AUTHORIZATION_SERVER;
        $this->clientId = $client_id ?: self::DEFAULT_CLIENT_ID;
        $this->clientSecret = $client_secret ?: self::DEFAULT_CLIENT_SECRET;
        $this->scope = $scope;
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
    protected function authClient(): ClientInterface
    {
        return new Client(new BasicAuth($this->clientId, $this->clientSecret), $this->auth_server);
    }

    /**
     * Retrieves access token and saves it to the token storage.
     *
     * @psalm-suppress InvalidCatch - Exception by interface can be caught in PHP >= 7.1.
     */
    private function getAccessToken(): void
    {
        if ($this->tokenStorage->getRefreshToken()) {
            $body = [
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->tokenStorage->getRefreshToken(),
            ];
        } else {
            $body = [
                'grant_type' => 'password',
                'username' => $this->username,
                'password' => $this->password,
            ];
            if (!empty($this->mfaToken)) {
                $body['mfa_token'] = $this->mfaToken;
            }
            if (!empty($this->scope)) {
                $body['scope'] = $this->scope;
            }
        }

        try {
            $response = $this->authClient()->post('', http_build_query($body), ['Content-Type' => 'application/x-www-form-urlencoded']);
            $this->tokenStorage->saveToken(json_decode((string) $response->getBody(), true));
        } catch (Exception $e) {
            throw new OauthAuthenticationException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
