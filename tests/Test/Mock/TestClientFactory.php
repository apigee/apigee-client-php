<?php

namespace Apigee\Edge\Tests\Test\Mock;

use Apigee\Edge\HttpClient\Client;
use Apigee\Edge\HttpClient\ClientInterface;
use Apigee\Edge\HttpClient\Util\Builder;
use Http\Client\HttpClient;
use Http\Message\Authentication\BasicAuth;
use Http\Mock\Client as MockClient;

/**
 * Class TestClientFactory.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class TestClientFactory
{
    public const OFFLINE_CLIENT_USER_AGENT_PREFIX = 'PHPUNIT';

    /**
     * Factory method that returns a configured API client.
     *
     * @param string|null $fqcn
     *   Fully qualified test name of the HTTP client class.
     *
     * @return ClientInterface
     */
    public function getClient(string $fqcn = null): ClientInterface
    {
        $fqcn = $fqcn ?: getenv('APIGEE_EDGE_PHP_SDK_HTTP_CLIENT') ?: FileSystemMockClient::class;
        $rc = new \ReflectionClass($fqcn);
        if (!$rc->implementsInterface(MockClientInterface::class) && !$rc->implementsInterface(HttpClient::class)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Class must implements either %s interface or %s interface.',
                    MockClientInterface::class,
                    HttpClient::class
                )
            );
        }
        $userAgentPrefix = '';
        if ($rc->implementsInterface(MockClientInterface::class) || $rc->isSubclassOf(MockClient::class)) {
            // The only way to identify whether this is mock HTTP client in tests is this special user agent prefix.
            $userAgentPrefix = self::OFFLINE_CLIENT_USER_AGENT_PREFIX;
        }
        $auth = null;
        $user = getenv('APIGEE_EDGE_PHP_SDK_BASIC_AUTH_USER') ?: '';
        $password = getenv('APIGEE_EDGE_PHP_SDK_BASIC_AUTH_PASSWORD') ?: '';
        if ($user || $password) {
            $auth = new BasicAuth($user, $password);
        }
        $endpoint = getenv('APIGEE_EDGE_PHP_SDK_ENDPOINT') ?: null;
        $builder = new Builder($rc->newInstance());

        return new Client($auth, $builder, $endpoint, $userAgentPrefix);
    }

    /**
     * Helper function that returns whether the API client is using a mock HTTP client or not.
     *
     * @param ClientInterface $client
     *   API client.
     *
     * @return bool
     */
    public static function isMockClient(ClientInterface $client): bool
    {
        return 0 === strpos($client->getUserAgent(), TestClientFactory::OFFLINE_CLIENT_USER_AGENT_PREFIX);
    }
}
