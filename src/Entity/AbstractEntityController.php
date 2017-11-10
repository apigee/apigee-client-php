<?php

namespace Apigee\Edge\Entity;

use Apigee\Edge\Exception\InvalidJsonException;
use Apigee\Edge\HttpClient\Client;
use Apigee\Edge\HttpClient\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class AbstractEntityController.
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
abstract class AbstractEntityController implements BaseEntityControllerInterface
{
    use CommonEntityPropertiesAwareTrait;

    /**
     * @var EntityFactoryInterface Entity factory that can return an entity which can be used as an internal
     * representation of the Apigee Edge API response.
     */
    protected $entityFactory;

    /**
     * @var ClientInterface Client interface that should be used for communication.
     */
    protected $client;

    /**
     * AbstractEntityController constructor.
     * @param ClientInterface|null $client
     * @param EntityFactoryInterface|null $entityFactory
     */
    public function __construct(ClientInterface $client = null, EntityFactoryInterface $entityFactory = null)
    {
        $this->client = $client ?: new Client();
        $this->entityFactory = $entityFactory ?: new EntityFactory();
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
     * @return UriInterface
     */
    protected function getEntityEndpointUri(string $entityId): UriInterface
    {
        return $this->getBaseEndpointUri()->withPath(sprintf('%s/%s', $this->getBaseEndpointUri(), $entityId));
    }

    /**
     * Parse an Apigee Edge API response to an associative array.
     *
     * @param ResponseInterface $response
     * @return array
     */
    protected function parseResponseToArray(ResponseInterface $response): array
    {
        $array = [];
        if ($response->getHeaderLine('Content-Type') &&
            strpos($response->getHeaderLine('Content-Type'), 'application/json') !== false) {
            $array = json_decode($response->getBody(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidJsonException(
                    json_last_error_msg(),
                    $response,
                    $this->client->getJournal()->getLastRequest()
                );
            }
        }
        // TODO Should we throw exception if the parsed input is empty?
        return $array;
    }

    /**
     * @inheritdoc
     */
    public function load(string $entityId): EntityInterface
    {
        $response = $this->client->get($this->getEntityEndpointUri($entityId));
        return $this->entityFactory->getEntityByController($this)::create($this->parseResponseToArray($response));
    }

    /**
     * @inheritdoc
     */
    public function save(EntityInterface $entity): EntityInterface
    {
        if (!$entity->id()) {
            // Create new entity, because its id field is empty.
            $response = $this->client->post($this->getBaseEndpointUri(), json_encode($entity));
        } else {
            $uri = $this->getEntityEndpointUri($entity->id());
            // Update an existing entity.
            $response = $this->client->put($uri, json_encode($entity));
        }
        return $this->entityFactory->getEntityByController($this)::create($this->parseResponseToArray($response));
    }

    /**
     * @inheritdoc
     */
    public function delete(string $entityId): EntityInterface
    {
        $response = $this->client->delete($this->getEntityEndpointUri($entityId));
        return $this->entityFactory->getEntityByController($this)::create($this->parseResponseToArray($response));
    }
}
