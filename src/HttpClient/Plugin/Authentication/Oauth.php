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

use Apigee\Edge\Exception\ApiResponseException;
use Apigee\Edge\Exception\OauthResponseException;
use Apigee\Edge\HttpClient\Client;
use Http\Message\Authentication;
use Http\Message\Authentication\Bearer;
use Psr\Http\Message\RequestInterface;

/**
 * Oauth authentication plugin for authenticating to Apigee Edge with Oauth (SAML).
 *
 * @see https://apidocs.apigee.com/api-reference/content/using-oauth2-security-apigee-edge-management-api
 */
final class Oauth implements Authentication
{
    /**
     * @var \Apigee\Edge\HttpClient\Client
     */
    private $client;

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
     * @var string
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
     * @param string $client_id
     *   Client id.
     * @param string $client_secret
     *   Client secret.
     * @param string $scope
     *   Oauth scope.
     * @param string $auth_server
     *   Authentication server.
     * @param string $userAgentPrefix
     *   User agent prefix used by the API client.
     */
    public function __construct(string $username, string $password, OauthTokenStorageInterface $token_storage, string $mfa_token = null, string $client_id = 'edgecli', string $client_secret = 'edgeclisecret', string $scope = '', string $auth_server = 'https://login.apigee.com/oauth/token', string $userAgentPrefix = '')
    {
        $this->client = new Client(null, null, $auth_server, $userAgentPrefix);
        $this->username = $username;
        $this->password = $password;
        $this->tokenStorage = $token_storage;
        $this->mfaToken = $mfa_token;
        $this->auth_server = $auth_server;
        $this->clientId = $client_id;
        $this->clientSecret = $client_secret;
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
     * Retrieves access token and saves it to the token storage.
     */
    private function getAccessToken(): void
    {
        $headers = [
            'Authorization' => sprintf('Basic %s', base64_encode(sprintf('%s:%s', $this->clientId, $this->clientSecret))),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        if ($refreshToken = $this->tokenStorage->getRefreshToken()) {
            $body = [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ];
        } else {
            $body = [
                'grant_type' => 'password',
                'username' => $this->username,
                'password' => $this->password,
            ];
            if (null !== $this->mfaToken) {
                $body['mfa_token'] = $this->mfaToken;
            }
            if (null !== $this->scope) {
                $body['scope'] = $this->scope;
            }
        }

        try {
            $response = $this->client->post(null, http_build_query($body), $headers);
            $this->tokenStorage->saveToken(json_decode((string) $response->getBody(), true));
        } catch (\Exception $e) {
            $code = $e->getCode();
            if ($e instanceof ApiResponseException) {
                $code = $e->getEdgeErrorCode();
            }
            throw new OauthResponseException($this->client->getJournal()->getLastResponse(), $this->client->getJournal()->getLastRequest(), $e->getMessage(), (int) $code, $e);
        }
    }
}
