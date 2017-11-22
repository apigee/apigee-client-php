<?php

namespace Apigee\Edge\Entity;

use Apigee\Edge\Exception\InvalidJsonException;
use Apigee\Edge\HttpClient\Client;
use Apigee\Edge\HttpClient\ClientInterface;
use Http\Message\Exception\UnexpectedValueException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * Class AbstractEntityController.
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
abstract class AbstractEntityController implements BaseEntityControllerInterface
{
    /**
     * @var EntityFactoryInterface Entity factory that can return an entity which can be used as an internal
     * representation of the Apigee Edge API response.
     */
    protected $entityFactory;

    /**
     * @var \Symfony\Component\Serializer\SerializerInterface
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
            [new EntityNormalizer()],
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
     * @return array
     *
     * @throws \RuntimeException If response can not be decoded as an array, because the input format is unknown.
     * @throws InvalidJsonException If there was an error with decoding a JSON response.
     */
    protected function parseResponseToArray(ResponseInterface $response): array
    {
        if ($response->getHeaderLine('Content-Type') &&
            strpos($response->getHeaderLine('Content-Type'), 'application/json') === 0) {
            try {
                return $this->entitySerializer->decode($response->getBody(), 'json');
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

    /**
     * @inheritdoc
     */
    public function load(string $entityId): EntityInterface
    {
        $response = $this->client->get($this->getEntityEndpointUri($entityId));
        return $this->entitySerializer->deserialize(
            $response->getBody(),
            get_class($this->entityFactory->getEntityByController($this)),
            'json'
        );
    }

    /**
     * @inheritdoc
     */
    public function create(EntityInterface $entity): EntityInterface
    {
        $response = $this->client->post(
            $this->getBaseEndpointUri(),
            $this->entitySerializer->serialize($entity, 'json')
        );
        return $this->entitySerializer->deserialize($response->getBody(), get_class($entity), 'json');
    }

    /**
     * @inheritdoc
     */
    public function update(EntityInterface $entity): EntityInterface
    {
        $uri = $this->getEntityEndpointUri($entity->id());
        // Update an existing entity.
        $response = $this->client->put(
            $uri,
            $this->entitySerializer->serialize($entity, 'json')
        );
        return $this->entitySerializer->deserialize($response->getBody(), get_class($entity), 'json');
    }

    /**
     * @inheritdoc
     */
    public function delete(string $entityId): EntityInterface
    {
        $response = $this->client->delete($this->getEntityEndpointUri($entityId));
        return $this->entitySerializer->deserialize(
            $response->getBody(),
            get_class($this->entityFactory->getEntityByController($this)),
            'json'
        );
    }
}
