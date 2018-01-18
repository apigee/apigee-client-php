<?php

namespace Apigee\Edge\Tests\Controller;

use Apigee\Edge\Controller\AbstractEntityController;
use Apigee\Edge\Entity\EntityFactoryInterface;
use Apigee\Edge\HttpClient\Client;
use Apigee\Edge\HttpClient\ClientInterface;
use Apigee\Edge\HttpClient\Util\Builder;
use Apigee\Edge\Tests\Test\Mock\MockHttpClient;
use GuzzleHttp\Psr7\Response;
use Http\Discovery\UriFactoryDiscovery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use function GuzzleHttp\Psr7\stream_for;

/**
 * Class AbstractEntityControllerTest.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @group controller
 * @group mock
 * @group offline
 * @small
 */
class AbstractEntityControllerTest extends TestCase
{
    private static $stub;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$stub = new class() extends AbstractEntityController {
            private $mockClient;

            private $rebuild = true;

            /**
             *  constructor.
             *
             * @param \Apigee\Edge\HttpClient\ClientInterface|null $client
             * @param \Apigee\Edge\Entity\EntityFactoryInterface|null $entityFactory
             */
            public function __construct(
                ClientInterface $client = null,
                EntityFactoryInterface $entityFactory = null
            ) {
                parent::__construct($client, $entityFactory);
                $this->mockClient = new MockHttpClient();
            }

            /**
             * @inheritdoc
             */
            protected function getBaseEndpointUri(): UriInterface
            {
                $uriFactory = UriFactoryDiscovery::find();

                return $uriFactory->createUri('');
            }

            /**
             * Allows to set returned response by the mock HTTP client.
             *
             * @param \Psr\Http\Message\ResponseInterface $response
             */
            public function addResponse(ResponseInterface $response): void
            {
                $this->mockClient->addResponse($response);
                $this->rebuild = true;
            }

            /**
             * @return \Apigee\Edge\HttpClient\Client
             */
            public function getClient(): Client
            {
                if ($this->rebuild) {
                    $builder = new Builder($this->mockClient);
                    $this->client = new Client(null, $builder);
                    $this->rebuild = false;
                }

                return $this->client;
            }

            /**
             * Exposes protected parseResponseToArray method for testing.
             *
             * @param \Psr\Http\Message\ResponseInterface $response
             *
             * @return array
             */
            public function toArray(ResponseInterface $response)
            {
                return $this->responseToArray($response);
            }
        };
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testParseResponseWithUnknownContentType(): void
    {
        /** @var \Apigee\Edge\HttpClient\ClientInterface $client */
        $client = static::$stub->getClient();
        static::$stub->addResponse(new Response());
        $response = $client->send('GET', '');
        static::$stub->toArray($response);
    }

    /**
     * @expectedException \Apigee\Edge\Exception\InvalidJsonException
     */
    public function testParseResponseWithInvalidJson(): void
    {
        /** @var \Apigee\Edge\HttpClient\ClientInterface $client */
        $client = static::$stub->getClient();
        static::$stub->addResponse(new Response(200, ['Content-Type' => 'application/json'], stream_for('')));
        $response = $client->send('GET', '');
        static::$stub->toArray($response);
    }
}
