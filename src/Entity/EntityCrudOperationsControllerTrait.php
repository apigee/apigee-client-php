<?php

namespace Apigee\Edge\Entity;

use Psr\Http\Message\ResponseInterface;

/**
 * Trait EntityCrudOperationsControllerTrait.
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 * @see \Apigee\Edge\Entity\EntityCrudOperationsControllerInterface
 */
trait EntityCrudOperationsControllerTrait
{
    /**
     * @inheritdoc
     */
    public function load(string $entityId): EntityInterface
    {
        $response = $this->client->get($this->getEntityEndpointUri($entityId));
        return $this->entitySerializer->deserialize(
            $response->getBody(),
            $this->entityFactory->getEntityTypeByController($this),
            'json'
        );
    }

    /**
     * @inheritdoc
     */
    public function create(EntityInterface $entity): void
    {
        $response = $this->client->post(
            $this->getBaseEndpointUri(),
            $this->entitySerializer->serialize($entity, 'json')
        );
        $this->setPropertiesFromResponse($response, $entity);
    }

    /**
     * @inheritdoc
     */
    public function update(EntityInterface $entity): void
    {
        $uri = $this->getEntityEndpointUri($entity->id());
        // Update an existing entity.
        $response = $this->client->put(
            $uri,
            $this->entitySerializer->serialize($entity, 'json')
        );
        $this->setPropertiesFromResponse($response, $entity);
    }

    /**
     * @inheritdoc
     */
    public function delete(string $entityId): EntityInterface
    {
        $response = $this->client->delete($this->getEntityEndpointUri($entityId));
        return $this->entitySerializer->deserialize(
            $response->getBody(),
            $this->entityFactory->getEntityTypeByController($this),
            'json'
        );
    }

    /**
     * Set property values on an entity from an Edge response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *   Response from Apigee Edge.
     * @param \Apigee\Edge\Entity\EntityInterface $entity
     *   Entity that properties should be updated.
     */
    private function setPropertiesFromResponse(ResponseInterface $response, EntityInterface $entity)
    {
        // Parse Edge response to a temporary entity (with the same type as $entity).
        // This is a crucial step because Edge response must be transformed before we would be able use it with some
        // of our setters (ex.: attributes).
        $tmp = $this->entitySerializer->deserialize(
            $response->getBody(),
            get_class($entity),
            'json'
        );
        $ro = new \ReflectionObject($entity);
        // Copy property values from the temporary entity to $entity.
        foreach ($ro->getProperties() as $property) {
            $setter = 'set' . ucfirst($property->getName());
            $getter = 'get' . ucfirst($property->getName());
            $rm = new \ReflectionMethod($entity, $setter);
            $value = $tmp->{$getter}();
            // Exclude null values.
            // (An entity property value is null (internally) if it is scalar and the Edge response from
            // the entity object has been created did not contain value for the property.)
            if ($value !== null) {
                $rm->invoke($entity, $value);
            }
        }
    }
}
