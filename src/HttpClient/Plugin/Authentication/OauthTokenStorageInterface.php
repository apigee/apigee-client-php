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

/**
 * Describes a storage that is used by the Oauth authentication plugin for storing and returning access token data.
 *
 * @see \Apigee\Edge\HttpClient\Plugin\Authentication\Oauth
 */
interface OauthTokenStorageInterface
{
    /**
     * Returns the access token.
     *
     * @return null|string
     */
    public function getAccessToken(): ?string;

    /**
     * Returns the token type.
     *
     * @return null|string
     */
    public function getTokenType(): ?string;

    /**
     * The UNIX timestamp when the token should be considered as expired.
     *
     * @return int
     */
    public function getExpires(): int;

    /**
     * Returns whether the token has expired or not.
     *
     * @return bool
     */
    public function hasExpired(): bool;

    /**
     * Marks an authentication token as expired.
     *
     * It can be used to request a new access token by using a saved refresh token.
     *
     * @see \Apigee\Edge\HttpClient\Plugin\RetryOauthAuthenticationPlugin
     */
    public function markExpired(): void;

    /**
     * Returns the refresh token.
     *
     * @return null|string
     */
    public function getRefreshToken(): ?string;

    /**
     * Returns the scope.
     *
     * @return string
     */
    public function getScope(): string;

    /**
     * Saves access token data.
     *
     * It is also recommended to calculate the expiration date of the token from the "expires_in" value.
     *
     * @param array $data
     *   Token data.
     */
    public function saveToken(array $data): void;

    /**
     * Removes saved token data from storage.
     *
     * It can be used to enforce authentication with client credentials to get a new access token.
     *
     * @see \Apigee\Edge\HttpClient\Plugin\RetryOauthAuthenticationPlugin
     */
    public function removeToken(): void;
}
