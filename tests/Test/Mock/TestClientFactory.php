<?php

namespace Apigee\Edge\Tests\Test\Mock;

use Apigee\Edge\HttpClient\Client;
use Apigee\Edge\HttpClient\ClientInterface;
use Apigee\Edge\HttpClient\Util\Builder;
use Http\Client\HttpClient;
use Http\Message\Authentication\BasicAuth;

/**
 * Class TestClientFactory.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class TestClientFactory
{
    public const OFFLINE_CLIENT_USER_AGENT_PREFIX = 'PHPUNIT';

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
        if ($rc->implementsInterface(MockClientInterface::class)) {
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
}
