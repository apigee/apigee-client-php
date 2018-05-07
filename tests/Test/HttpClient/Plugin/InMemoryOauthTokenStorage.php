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

namespace Apigee\Edge\Tests\Test\HttpClient\Plugin;

use Apigee\Edge\HttpClient\Plugin\Authentication\OauthTokenStorageInterface;

/**
 * Oauth token storage implementation that stores token data in memory until the code runs.
 */
final class InMemoryOauthTokenStorage implements OauthTokenStorageInterface
{
    /**
     * Token data storage.
     *
     * @var array
     */
    private $storage = [];

    /**
     * The timestamp when the token expires.
     *
     * This value is calculated from "expires_in" value which is the lifetime of the access token in seconds.
     *
     * @var int
     */
    private $expires = 0;

    /**
     * Number of seconds extracted from token's expiration date when hasExpired() calculates.
     *
     * This ensures that token gets refreshed earlier than it expires.
     *
     * @var int
     */
    private $leeway;

    /**
     * InMemoryOauthTokenStorage constructor.
     *
     * @param int $leeway
     *   Number of seconds that is extracted from token's expiration date to ensure we refreshes the access token
     *   before it gets expired.
     */
    public function __construct(int $leeway = 30)
    {
        $this->leeway = $leeway;
    }

    /**
     * @inheritdoc
     */
    public function getAccessToken(): ?string
    {
        return array_key_exists('access_token', $this->storage) ? $this->storage['access_token'] : null;
    }

    /**
     * @inheritdoc
     */
    public function getTokenType(): ?string
    {
        return array_key_exists('token_type', $this->storage) ? $this->storage['token_type'] : null;
    }

    /**
     * @inheritdoc
     */
    public function getExpires(): int
    {
        return $this->expires;
    }

    /**
     * @inheritdoc
     */
    public function hasExpired(): bool
    {
        if (0 !== $this->getExpires() && ($this->getExpires() - $this->leeway) > time()) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getRefreshToken(): ?string
    {
        return array_key_exists('refresh_token', $this->storage) ? $this->storage['refresh_token'] : null;
    }

    /**
     * @inheritdoc
     */
    public function getScope(): string
    {
        return array_key_exists('scope', $this->storage) ? $this->storage['scope'] : null;
    }

    /**
     * @inheritdoc
     */
    public function saveToken(array $data): void
    {
        $this->storage = $data;
        if (array_key_exists('expires_in', $this->storage) && 0 !== $this->storage['expires_in']) {
            $this->expires = $this->storage['expires_in'] + time();
        }
    }

    /**
     * @inheritdoc
     */
    public function markExpired(): void
    {
        $this->expires = 0;
    }

    /**
     * @inheritdoc
     */
    public function removeToken(): void
    {
        $this->storage = [];
    }
}
