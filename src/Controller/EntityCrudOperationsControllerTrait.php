<?php

/*
 * Copyright 2018 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Apigee\Edge\Controller;

use Apigee\Edge\Entity\EntityInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Trait EntityCrudOperationsControllerTrait.
 *
 * @see \Apigee\Edge\Controller\EntityCrudOperationsControllerInterface
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
            (string) $response->getBody(),
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
     *
     * @psalm-suppress PossiblyNullArgument $entity->id() is always not null here.
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
            (string) $response->getBody(),
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
     *
     * @throws \ReflectionException
     */
    private function setPropertiesFromResponse(ResponseInterface $response, EntityInterface $entity): void
    {
        // Parse Edge response to a temporary entity (with the same type as $entity).
        // This is a crucial step because Edge response must be transformed before we would be able use it with some
        // of our setters (ex.: attributes).
        $tmp = $this->entitySerializer->deserialize(
            (string) $response->getBody(),
            get_class($entity),
            'json'
        );
        $ro = new \ReflectionObject($entity);
        // Copy property values from the temporary entity to $entity.
        foreach ($ro->getProperties() as $property) {
            $setter = 'set' . ucfirst($property->getName());
            $getter = 'get' . ucfirst($property->getName());
            // Ensure that these methods are exist. This is always true for all SDK entities but we can not be sure
            // about custom implementation.
            if ($ro->hasMethod($setter) && $ro->hasMethod($getter)) {
                $rm = new \ReflectionMethod($entity, $setter);
                $value = $tmp->{$getter}();
                // Exclude null values.
                // (An entity property value is null (internally) if it is scalar and the Edge response from
                // the entity object has been created did not contain value for the property.)
                if (null !== $value) {
                    $rm->invoke($entity, $value);
                }
            }
        }
    }
}
