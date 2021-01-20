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
use Apigee\Edge\Exception\OauthRefreshTokenExpiredException;
use Http\Client\Exception;
use Http\Message\Authentication\BasicAuth;

/**
 * Oauth authentication plugin for authenticating to Apigee Edge with Oauth (SAML).
 *
 * @see https://apidocs.apigee.com/api-reference/content/using-oauth2-security-apigee-edge-management-api
 */
class Oauth extends AbstractOauth
{
    public const DEFAULT_AUTHORIZATION_SERVER = 'https://login.apigee.com/oauth/token';

    public const DEFAULT_CLIENT_ID = 'edgecli';

    public const DEFAULT_CLIENT_SECRET = 'edgeclisecret';

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string|null
     */
    protected $mfaToken;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var string|null
     */
    protected $scope;

    /**
     * Oauth constructor.
     *
     * @param string $username
     *   Apigee Edge username (email).
     * @param string $password
     *   Apigee Edge password.
     * @param \Apigee\Edge\HttpClient\Plugin\Authentication\OauthTokenStorageInterface $tokenStorage
     *   Storage where access token gets saved.
     * @param string|null $mfaToken
     *   One-time multi-factor authentication code.
     * @param string|null $clientId
     *   Client id.
     * @param string|null $clientSecret
     *   Client secret.
     * @param string|null $scope
     *   Oauth scope.
     * @param string|null $authServer
     *   Authentication server.
     */
    public function __construct(string $username, string $password, OauthTokenStorageInterface $tokenStorage, ?string $mfaToken = null, ?string $clientId = null, ?string $clientSecret = null, ?string $scope = null, ?string $authServer = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->mfaToken = $mfaToken;
        $this->clientId = $clientId ?: self::DEFAULT_CLIENT_ID;
        $this->clientSecret = $clientSecret ?: self::DEFAULT_CLIENT_SECRET;
        $this->scope = $scope;
        parent::__construct($tokenStorage, $authServer ?: self::DEFAULT_AUTHORIZATION_SERVER);
    }

    /**
     * @inheritdoc
     */
    protected function authClient(): ClientInterface
    {
        return new Client(new BasicAuth($this->clientId, $this->clientSecret), $this->authServer);
    }

    /**
     * @inheritdoc
     *
     * @psalm-suppress InvalidCatch - Exception by interface can be caught in PHP >= 7.1.
     */
    protected function getAccessToken(): void
    {
        if ($this->tokenStorage->getRefreshToken()) {
            $body = [
                'grant_type=refresh_token',
                "refresh_token={$this->tokenStorage->getRefreshToken()}",
            ];
        } else {
            $body = [
                'grant_type=password',
                "username={$this->username}",
                "password={$this->password}",
            ];
            if (!empty($this->mfaToken)) {
                $body[] = "mfa_token={$this->mfaToken}";
            }
            if (!empty($this->scope)) {
                $body[] = "scope={$this->scope}";
            }
        }

        try {
            // html_build_query() would encode special characters in values which could lead to a malformed payload.
            // This solution here is similar to what common PSR-7 implementations provides in an extra function
            // (like \GuzzleHttp\Psr7\build_query()) besides a PSR-7 implementation.
            $response = $this->authClient()->post('', implode('&', $body), ['Content-Type' => 'application/x-www-form-urlencoded']);
            $this->tokenStorage->saveToken(json_decode((string) $response->getBody(), true));
        } catch (OauthRefreshTokenExpiredException $e) {
            // Clear data in token storage because refresh token has expired.
            $this->tokenStorage->removeToken();
            // Try to automatically get a new access token by sending client
            // id and secret.
            $this->getAccessToken();
        } catch (Exception $e) {
            throw new OauthAuthenticationException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
