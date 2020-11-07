<?php

/*
 * Copyright 2020 Google LLC
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
use Http\Client\Exception;
use Http\Message\Authentication\Header;

/**
 * GCE Service Account authentication plugin for authenticating to Google
 * Cloud API.
 *
 * @see https://developers.google.com/identity/protocols/OAuth2ServiceAccount
 */
class GceServiceAccount extends AbstractOauth
{
    public const DEFAULT_GCE_AUTH_SERVER = 'http://metadata.google.internal/computeMetadata/v1/instance/service-accounts/default/token';

    /**
     * GceServiceAccountAuthentication constructor.
     *
     * @param \Apigee\Edge\HttpClient\Plugin\Authentication\OauthTokenStorageInterface $tokenStorage
     *   Storage where access token gets saved.
     */
    public function __construct(OauthTokenStorageInterface $tokenStorage)
    {
        parent::__construct($tokenStorage, static::DEFAULT_GCE_AUTH_SERVER);
    }

    /**
     * Validate if the GCE Auth Server URL is reachable.
     *
     * This is only available on GCP.
     *
     * @return bool
     *   If GCE Service Account authentication is available.
     */
    public function isAvailable(): bool
    {
        try {
            $this->authClient()->get('');
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    protected function authClient(): ClientInterface
    {
        return new Client($this->getAuthHeader(), $this->getAuthServer());
    }

    /**
     * @inheritdoc
     *
     * @psalm-suppress InvalidCatch - Exception by interface can be caught in PHP >= 7.1.
     */
    protected function getAccessToken(): void
    {
        try {
            $response = $this->authClient()->get('');
            $decoded_token = json_decode((string) $response->getBody(), true);
            $this->tokenStorage->saveToken($decoded_token);
        } catch (Exception $e) {
            throw new HybridOauth2AuthenticationException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Return the Auth Header required by GCE Access token endpoint.
     *
     * @return \Http\Message\Authentication\Header
     */
    protected function getAuthHeader(): Header
    {
        return new Header('Metadata-Flavor', 'Google');
    }
}
