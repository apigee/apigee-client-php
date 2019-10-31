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

use Apigee\Edge\Client;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Exception\HybridOauth2AuthenticationException;
use Apigee\Edge\Exception\OauthRefreshTokenExpiredException;
use DomainException;
use Firebase\JWT\JWT;
use Http\Client\Exception;

/**
 * HybridOauth2 authentication plugin for authenticating to Hybrid Cloud API.
 *
 * @see https://developers.google.com/identity/protocols/OAuth2ServiceAccount
 */
class HybridOauth2 extends AbstractOauth
{
    /**
     * Authorization server for Apigee Hybrid authentication.
     *
     * @var string
     */
    public const DEFAULT_AUTHORIZATION_SERVER = 'https://oauth2.googleapis.com/token';

    /**
     * A space-delimited list of the permissions that the application requests.
     *
     * @var string
     */
    public const TOKEN_SCOPES = 'https://www.googleapis.com/auth/cloud-platform';

    /**
     * Grant type used in an access token request.
     *
     * @see https://developers.google.com/identity/protocols/OAuth2ServiceAccount
     *
     * @var string
     */
    public const GRANT_TYPE = 'urn:ietf:params:oauth:grant-type:jwt-bearer';

    /**
     * The service account email.
     *
     * @var string
     */
    protected $email;

    /**
     * The service account private key.
     *
     * @var string
     */
    protected $privateKey;

    /**
     * Hybrid Oauth2 constructor.
     *
     * @param string $email
     *   The service account email.
     * @param string $privateKey
     *   The service account private key.
     * @param \Apigee\Edge\HttpClient\Plugin\Authentication\OauthTokenStorageInterface $tokenStorage
     *   Storage where access token gets saved.
     * @param string|null $authServer
     *   Authentication server.
     */
    public function __construct(string $email, string $privateKey, OauthTokenStorageInterface $tokenStorage, ?string $authServer = null)
    {
        $this->email = $email;
        $this->privateKey = $privateKey;
        parent::__construct($tokenStorage, $authServer ?: self::DEFAULT_AUTHORIZATION_SERVER);
    }

    /**
     * @inheritdoc
     */
    protected function authClient(): ClientInterface
    {
        return new Client(new NullAuthentication(), $this->authServer);
    }

    /**
     * @inheritdoc
     *
     * @psalm-suppress InvalidCatch - Exception by interface can be caught in PHP >= 7.1.
     */
    protected function getAccessToken(): void
    {
        $now = time();
        $token = [
            'iss' => $this->email,
            'aud' => $this->authServer,
            'scope' => self::TOKEN_SCOPES,
            'iat' => $now,
            // Have the token expire in the maximum allowed time of an hour.
            'exp' => $now + (60 * 60),
        ];

        try {
            $jwt = JWT::encode($token, $this->privateKey, 'RS256');
        }
        catch (DomainException $e) {
            throw new HybridOauth2AuthenticationException($e->getMessage(), $e->getCode(), $e);
        }

        $body = [
            'grant_type' => self::GRANT_TYPE,
            'assertion' => $jwt,
        ];

        try {
            $response = $this->authClient()->post('', http_build_query($body), ['Content-Type' => 'application/x-www-form-urlencoded']);
            $decodedResponse = json_decode((string) $response->getBody(), true);
            $this->tokenStorage->saveToken($decodedResponse);
        } catch (OauthRefreshTokenExpiredException $e) {
            // Clear data in token storage because refresh token has expired.
            $this->tokenStorage->removeToken();
            // Try to automatically get a new access token.
            $this->getAccessToken();
        } catch (Exception $e) {
            throw new HybridOauth2AuthenticationException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
