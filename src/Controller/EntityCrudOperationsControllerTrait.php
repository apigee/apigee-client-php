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

/**
 * Trait EntityCrudOperationsControllerTrait.
 *
 * @see \Apigee\Edge\Controller\EntityCrudOperationsControllerInterface
 */
trait EntityCrudOperationsControllerTrait
{
    use BaseEndpointAwareControllerTrait;
    use ClientAwareControllerTrait;
    use EntityClassAwareTrait;
    use EntityEndpointAwareControllerTrait;
    use EntityTransformerAwareTrait;

    /**
     * @inheritdoc
     */
    public function load(string $entityId): EntityInterface
    {
        $response = $this->getClient()->get($this->getEntityEndpointUri($entityId));

        return $this->getEntityTransformer()->deserialize(
            (string) $response->getBody(),
            $this->getEntityClass(),
            'json'
        );
    }

    /**
     * @inheritdoc
     */
    public function create(EntityInterface $entity): void
    {
        $response = $this->getClient()->post(
            $this->getBaseEndpointUri(),
            $this->getEntityTransformer()->serialize($entity, 'json')
        );
        $this->getEntityTransformer()->setPropertiesFromResponse($response, $entity);
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
        $response = $this->getClient()->put(
            $uri,
            $this->getEntityTransformer()->serialize($entity, 'json')
        );
        $this->getEntityTransformer()->setPropertiesFromResponse($response, $entity);
    }

    /**
     * @inheritdoc
     */
    public function delete(string $entityId): EntityInterface
    {
        $response = $this->getClient()->delete($this->getEntityEndpointUri($entityId));

        return $this->getEntityTransformer()->deserialize(
            (string) $response->getBody(),
            $this->getEntityClass(),
            'json'
        );
    }
}
