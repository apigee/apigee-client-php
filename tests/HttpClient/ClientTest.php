<?php

namespace Apigee\Edge\Tests\HttpClient;

use Apigee\Edge\HttpClient\Client;
use Apigee\Edge\HttpClient\Util\Builder;
use Apigee\Edge\Tests\Test\Mock\MockHttpClient;
use Http\Client\Common\Plugin\HeaderAppendPlugin;
use PHPUnit\Framework\TestCase;

/**
 * Class ClientTest.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @group client
 * @group mock
 * @group offline
 * @small
 *
 * TODO Add test for cache related methods later.
 */
class ClientTest extends TestCase
{
    /** @var \Apigee\Edge\Tests\Test\Mock\MockHttpClient */
    protected static $httpClient;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        // Use the Mock HTTP Client for all requests.
        self::$httpClient = new MockHttpClient();
        parent::setUpBeforeClass();
    }

    public function testDefaultConfiguration()
    {
        $builder = new Builder(self::$httpClient);
        $client = new Client(null, $builder);
        $client->get('/');
        $sent_request = self::$httpClient->getLastRequest();
        $this->assertEquals('https://api.enterprise.apigee.com/v1/', (string) $sent_request->getUri());
        $this->assertEquals($client->getUserAgent(), $sent_request->getHeaderLine('User-Agent'));
        $this->assertEquals('application/json; charset=utf-8', $sent_request->getHeaderLine('Accept'));

        return $client;
    }

    /**
     * @depends testDefaultConfiguration
     *
     * @param \Apigee\Edge\HttpClient\Client $client
     */
    public function testEndpointShouldBeOverridden(Client $client): void
    {
        $originalClient = clone $client;
        $onPremHost = 'http://on-prem-edge.dev';
        $client->setEndpoint($onPremHost);
        $this->assertNotEquals($originalClient, $client);
        $client->get('/');
        $sent_request = self::$httpClient->getLastRequest();
        $this->assertEquals(
            $onPremHost,
            sprintf('%s://%s', $sent_request->getUri()->getScheme(), $sent_request->getUri()->getHost())
        );
        $builder = new Builder(self::$httpClient);
        $client = new Client(null, $builder, $onPremHost);
        $client->get('/');
        $sent_request = self::$httpClient->getLastRequest();
        $this->assertEquals(
            $onPremHost,
            sprintf('%s://%s', $sent_request->getUri()->getScheme(), $sent_request->getUri()->getHost())
        );
    }

    public function testUserAgentShouldBeOverridden(): void
    {
        $builder = new Builder(self::$httpClient);
        $userAgentPrefix = 'Awesome ';
        $client = new Client(null, $builder, null, $userAgentPrefix);
        $originalClient = clone $client;
        $client->get('/');
        $sent_request = self::$httpClient->getLastRequest();
        $this->assertEquals(
            sprintf('%s (%s)', $userAgentPrefix, $client->getClientVersion()),
            $sent_request->getHeaderLine('User-Agent')
        );
        $userAgentPrefix = 'Super Awesome ';
        $client->setUserAgentPrefix($userAgentPrefix);
        $client->get('/');
        $sent_request = self::$httpClient->getLastRequest();
        $this->assertNotEquals($originalClient, $client);
        $this->assertEquals(
            sprintf('%s (%s)', $userAgentPrefix, $client->getClientVersion()),
            $sent_request->getHeaderLine('User-Agent')
        );
    }

    public function testRebuildShouldNotRemoveCustomPlugin(): void
    {
        $builder = new Builder(self::$httpClient);
        $builder->addPlugin(new HeaderAppendPlugin(['Foo' => 'bar']));
        $client = new Client(null, $builder);
        $originalClient = clone $client;
        $client->get('/');
        $sent_request = self::$httpClient->getLastRequest();
        $this->assertEquals('bar', $sent_request->getHeaderLine('Foo'));
        // Trigger something that rebuilds the underlying HTTP client.
        $client->setEndpoint('http://example.com');
        $this->assertNotEquals($originalClient, $client);
        $client->get('/');
        $sent_request = self::$httpClient->getLastRequest();
        $this->assertEquals('bar', $sent_request->getHeaderLine('Foo'));
    }
}
