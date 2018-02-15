<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

/**
 * Provides a client factory that can build a API client. It reads necessary credentials from environment variables.
 *
 * Requires the following environment variables to be set on your system:
 * - APIGEE_EDGE_PHP_SDK_BASIC_AUTH_USER
 * - APIGEE_EDGE_PHP_SDK_BASIC_AUTH_PASSWORD
 * - APIGEE_EDGE_PHP_SDK_ORGANIZATION
 *
 * Optional:
 * - APIGEE_EDGE_PHP_SDK_ENDPOINT
 * - APIGEE_EDGE_PHP_SDK_ENVIRONMENT
 */
use Apigee\Edge\HttpClient\Client;
use Apigee\Edge\HttpClient\ClientInterface;
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
     * @inheritdoc
     */
    public function getOrganization(): string
    {
        return getenv('APIGEE_EDGE_PHP_SDK_ORGANIZATION') ?: 'my-organization';
    }

    /**
     * @inheritdoc
     */
    public function getUsername(): string
    {
        return getenv('APIGEE_EDGE_PHP_SDK_BASIC_AUTH_USER') ?: 'my-email-address@example.com';
    }

    /**
     * @inheritdoc
     */
    public function getPassword(): string
    {
        return getenv('APIGEE_EDGE_PHP_SDK_BASIC_AUTH_PASSWORD') ?: 'my-secure-password';
    }
}

/**
 * Creates API clients.
 */
class ClientFactory
{
    /** @var null|string */
    private $endpoint;

    /** @var \CredentialProvider */
    private $credential;

    /** @var null|\Apigee\Edge\HttpClient\ClientInterface */
    private $client;

    /**
     * ClientFactory constructor.
     *
     * @param \CredentialProvider $credential
     *   Apigee Edge credentials.
     * @param null|string $endpoint
     *   Apigee Edge endpoint, if null it falls back to the url of Apigee Edge cloud.
     */
    public function __construct(CredentialProvider $credential, ?string $endpoint = null)
    {
        $this->endpoint = $endpoint ?: getenv('APIGEE_EDGE_PHP_SDK_ENDPOINT') ?: null;
        $this->credential = $credential;
    }

    /**
     * Returns a configured API client.
     *
     * @return \Apigee\Edge\HttpClient\ClientInterface
     */
    public function getClient(): ClientInterface
    {
        if (null == $this->client) {
            $this->client = new Client(
                new BasicAuth($this->credential->getUsername(), $this->credential->getPassword()), null, $this->endpoint
            );
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
