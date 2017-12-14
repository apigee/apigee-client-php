<?php

namespace Apigee\Edge\Controller;

use Apigee\Edge\Entity\EntityDenormalizer;
use Apigee\Edge\Entity\EntityFactory;
use Apigee\Edge\Entity\EntityFactoryInterface;
use Apigee\Edge\Entity\EntityNormalizer;
use Apigee\Edge\Exception\InvalidJsonException;
use Apigee\Edge\HttpClient\Client;
use Apigee\Edge\HttpClient\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Serializer;

/**
 * Class AbstractEntityController.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
abstract class AbstractEntityController
{
    /**
     * @var EntityFactoryInterface Entity factory that can return an entity which can be used as an internal
     * representation of the Apigee Edge API response.
     */
    protected $entityFactory;

    /**
     * @var \Symfony\Component\Serializer\Serializer
     */
    protected $entitySerializer;

    /**
     * @var ClientInterface Client interface that should be used for communication.
     */
    protected $client;

    /**
     * AbstractEntityController constructor.
     *
     * @param ClientInterface|null $client
     * @param EntityFactoryInterface|null $entityFactory
     */
    public function __construct(
        ClientInterface $client = null,
        EntityFactoryInterface $entityFactory = null
    ) {
        $this->client = $client ?: new Client();
        $this->entityFactory = $entityFactory ?: new EntityFactory();
        $this->entitySerializer = new Serializer(
            [new EntityNormalizer(), new EntityDenormalizer()],
            [new JsonEncoder()]
        );
    }

    /**
     * Returns the API endpoint that the controller communicates with.
     *
     * In case of an entity that belongs to an organisation it should return organization/[orgName]/[endpoint].
     *
     * @return UriInterface
     */
    abstract protected function getBaseEndpointUri(): UriInterface;

    /**
     * Returns the entity type specific base url for an API call.
     *
     * @param string $entityId
     *
     * @return UriInterface
     */
    protected function getEntityEndpointUri(string $entityId): UriInterface
    {
        return $this->getBaseEndpointUri()->withPath(sprintf('%s/%s', $this->getBaseEndpointUri(), $entityId));
    }

    /**
     * Parse an Apigee Edge API response to an associative array.
     *
     * The SDK only works with JSON responses, but let's be prepared for the unexpected.
     *
     * @param ResponseInterface $response
     *
     * @throws \RuntimeException If response can not be decoded as an array, because the input format is unknown.
     * @throws InvalidJsonException If there was an error with decoding a JSON response.
     *
     * @return array
     */
    protected function parseResponseToArray(ResponseInterface $response): array
    {
        if ($response->getHeaderLine('Content-Type') &&
            0 === strpos($response->getHeaderLine('Content-Type'), 'application/json')) {
            try {
                return $this->entitySerializer->decode((string) $response->getBody(), 'json');
            } catch (UnexpectedValueException $e) {
                throw new InvalidJsonException(
                    $e->getMessage(),
                    $response,
                    $this->client->getJournal()->getLastRequest()
                );
            }
        }
        throw new \RuntimeException(
            sprintf(
                'Unable to parse response with %s type. Response body: %s',
                $response->getHeaderLine('Content-Type') ?: 'unknown',
                $response->getBody()
            )
        );
    }
}
