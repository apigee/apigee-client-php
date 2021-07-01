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

/*
 * Provides a client factory that can build a API client. It reads necessary credentials from environment variables.
 *
 * Requires the following environment variables to be set on your system:
 * - APIGEE_EDGE_PHP_CLIENT_BASIC_AUTH_USER
 * - APIGEE_EDGE_PHP_CLIENT_BASIC_AUTH_PASSWORD
 * - APIGEE_EDGE_PHP_CLIENT_ORGANIZATION
 *
 * Optional:
 * - APIGEE_EDGE_PHP_CLIENT_ENDPOINT
 * - APIGEE_EDGE_PHP_CLIENT_ENVIRONMENT
 */
use Apigee\Edge\Client;
use Apigee\Edge\ClientInterface;
use Http\Message\Authentication\BasicAuth;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Interface CredentialProvider.
 */
interface CredentialProvider
{
    /**
     * Returns the organization.
     *
     * @return string
     */
    public function getOrganization(): string;

    /**
     * Returns the username.
     *
     * @return string
     */
    public function getUsername(): string;

    /**
     * Returns the password.
     *
     * @return string
     */
    public function getPassword(): string;
}

/**
 * Reads the Apigee Edge credentials from environment variables.
 */
class EnvironmentCredentialProvider implements CredentialProvider
{
    /**
     * {@inheritdoc}
     */
    public function getOrganization(): string
    {
        return getenv('APIGEE_EDGE_PHP_CLIENT_ORGANIZATION') ?: 'my-organization';
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): string
    {
        return getenv('APIGEE_EDGE_PHP_CLIENT_BASIC_AUTH_USER') ?: 'my-email-address@example.com';
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): string
    {
        return getenv('APIGEE_EDGE_PHP_CLIENT_BASIC_AUTH_PASSWORD') ?: 'my-secure-password';
    }
}

/**
 * Creates API clients.
 */
class ClientFactory
{
    /** @var string|null */
    private $endpoint;

    /** @var \CredentialProvider */
    private $credential;

    /** @var \Apigee\Edge\ClientInterface|null */
    private $client;

    /**
     * ClientFactory constructor.
     *
     * @param \CredentialProvider $credential
     *   Apigee Edge credentials.
     * @param string|null $endpoint
     *   Apigee Edge endpoint, if null it falls back to the url of Apigee Edge cloud.
     */
    public function __construct(CredentialProvider $credential, ?string $endpoint = null)
    {
        $this->endpoint = $endpoint ?: getenv('APIGEE_EDGE_PHP_CLIENT_ENDPOINT') ?: null;
        $this->credential = $credential;
    }

    /**
     * Returns a configured API client.
     *
     * @return \Apigee\Edge\ClientInterface
     */
    public function getClient(): ClientInterface
    {
        if (null == $this->client) {
            $this->client = new Client(new BasicAuth($this->credential->getUsername(), $this->credential->getPassword()), $this->endpoint);
        }

        return $this->client;
    }

    /**
     * Returns the organization for those API endpoints that belongs to an organization.
     *
     * @return string
     *   Organization name.
     */
    public function getOrganization(): string
    {
        return $this->credential->getOrganization();
    }
}

$clientFactory = new ClientFactory(new EnvironmentCredentialProvider());
