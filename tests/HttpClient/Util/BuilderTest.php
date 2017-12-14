<?php

namespace Apigee\Edge\Tests\HttpClient\Util;

use Apigee\Edge\HttpClient\Util\Builder;
use Apigee\Edge\Tests\Test\Mock\MockHttpClient;
use GuzzleHttp\Psr7\Request;
use Http\Client\Common\Plugin;
use Http\Client\Common\Plugin\CachePlugin;
use Http\Discovery\UriFactoryDiscovery;
use Http\Message\StreamFactory;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class BuilderTest.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @group client
 * @group mock
 * @group offline
 * @small
 */
class BuilderTest extends TestCase
{
    /** @var \Apigee\Edge\Tests\Test\Mock\MockHttpClient */
    protected static $httpClient;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        // Use the Mock HTTP Client for all requests.
        self::$httpClient = new MockHttpClient();
        parent::setUpBeforeClass();
    }

    /**
     * @small
     */
    public function testShouldReturnTheSameInstance()
    {
        $builder = new Builder(self::$httpClient);
        $client = $builder->getHttpClient();
        $this->assertEquals($client, $builder->getHttpClient());
    }

    public function testShouldSetHeaders()
    {
        $headers = ['Foo' => 'bar', 'Bar' => 'baz'];
        $builder = new Builder(self::$httpClient);
        $client = $builder->getHttpClient();
        $builder->setHeaders($headers);
        $this->assertNotEquals($client, $builder->getHttpClient());
        $builder->getHttpClient()->sendRequest(new Request('GET', 'http://example.com'));
        $sent_request = self::$httpClient->getLastRequest();
        $this->assertEquals($sent_request->getHeaderLine('Foo'), $headers['Foo']);

        return $builder;
    }

    /**
     * @depends testShouldSetHeaders
     *
     * @param \Apigee\Edge\HttpClient\Util\Builder $builder
     */
    public function testShouldSetHeaderValue(Builder $builder)
    {
        $client = $builder->getHttpClient();
        $builder->setHeaderValue('Foo', 'baz');
        $builder->setHeaderValue('Apigee', 'Edge');
        $this->assertNotEquals($client, $builder->getHttpClient());
        $request = new Request('GET', 'http://example.com');
        $builder->getHttpClient()->sendRequest($request);
        $sent_request = self::$httpClient->getLastRequest();
        $this->assertEquals('bar, baz', $sent_request->getHeaderLine('Foo'));
        $this->assertEquals('Edge', $sent_request->getHeaderLine('Apigee'));
    }

    /**
     * @depends testShouldSetHeaders
     *
     * @param \Apigee\Edge\HttpClient\Util\Builder $builder
     */
    public function testShouldRemoveHeader(Builder $builder)
    {
        $client = $builder->getHttpClient();
        $builder->removeHeader('Foo');
        $this->assertNotEquals($client, $builder->getHttpClient());
        $request = new Request('GET', 'http://example.com');
        $builder->getHttpClient()->sendRequest($request);
        $sent_request = self::$httpClient->getLastRequest();
        $this->assertArrayNotHasKey('Foo', $sent_request->getHeaders());
    }

    /**
     * @depends testShouldSetHeaders
     *
     * @param \Apigee\Edge\HttpClient\Util\Builder $builder
     */
    public function testShouldRemoveAllHeaders(Builder $builder)
    {
        $client = $builder->getHttpClient();
        $builder->clearHeaders();
        $this->assertNotEquals($client, $builder->getHttpClient());
        $request = new Request('GET', 'http://example.com');
        $builder->getHttpClient()->sendRequest($request);
        $sent_request = self::$httpClient->getLastRequest();
        $this->assertArrayNotHasKey('Foo', $sent_request->getHeaders());
        $this->assertArrayNotHasKey('Bar', $sent_request->getHeaders());
        $this->assertArrayNotHasKey('Apigee', $sent_request->getHeaders());
    }

    public function testShouldAddPlugin()
    {
        $builder = new Builder(self::$httpClient);
        $client = $builder->getHttpClient();
        $uriFactory = UriFactoryDiscovery::find();
        $addPathPlugin = new Plugin\AddPathPlugin($uriFactory->createUri('edge'));
        $builder->addPlugin($addPathPlugin);
        $this->assertNotEquals($client, $builder->getHttpClient());
        $request = new Request('GET', 'http://example.com');
        $builder->getHttpClient()->sendRequest($request);
        $sent_request = self::$httpClient->getLastRequest();
        $this->assertEquals('/edge', $sent_request->getUri()->getPath());

        return $builder;
    }

    /**
     * @depends testShouldAddPlugin
     *
     * @param \Apigee\Edge\HttpClient\Util\Builder $builder
     */
    public function testShouldRemovePlugin(Builder $builder)
    {
        $client = $builder->getHttpClient();
        $builder->removePlugin(Plugin\AddPathPlugin::class);
        $this->assertNotEquals($client, $builder->getHttpClient());
        $request = new Request('GET', 'http://example.com');
        $builder->getHttpClient()->sendRequest($request);
        $sent_request = self::$httpClient->getLastRequest();
        $this->assertEmpty($sent_request->getUri()->getPath());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldNotAddCachePlugin()
    {
        $cachePoolMock = $this->createMock(CacheItemPoolInterface::class);
        $streamFactoryMock = $this->createMock(StreamFactory::class);
        $builder = new Builder();
        $builder->addPlugin(new CachePlugin($cachePoolMock, $streamFactoryMock));
    }

    public function testShouldAddCachePlugin()
    {
        $cachePoolMock = $this->createMock(CacheItemPoolInterface::class);
        $builder = new Builder();
        $client = $builder->getHttpClient();
        $builder->addCache($cachePoolMock);
        $this->assertNotEquals($client, $builder->getHttpClient());

        return $builder;
    }

    /**
     * @depends testShouldAddCachePlugin
     */
    public function testShouldRemoveCachePlugin(Builder $builder)
    {
        $client = $builder->getHttpClient();
        $builder->removeCache();
        $this->assertNotEquals($client, $builder->getHttpClient());
    }
}
