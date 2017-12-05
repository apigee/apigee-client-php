<?php

namespace Apigee\Edge\Entity;

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
            $this->entityFactory->getEntityTypeByController($this),
            'json'
        );
    }
}
