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
 * Trait EntityCreateOperationControllerTrait.
 *
 * @see \Apigee\Edge\Controller\EntityCreateOperationControllerInterface
 */
trait EntityCreateOperationControllerTrait
{
    use ClientAwareControllerTrait;
    use EntitySerializerAwareTrait;

    /**
     * @inheritdoc
     */
    public function create(EntityInterface $entity): void
    {
        $response = $this->getClient()->post(
            $this->getBaseEndpointUri(),
            $this->buildEntityCreatePayload($entity)
        );
        $this->getEntitySerializer()->setPropertiesFromResponse($response, $entity);
    }

    /**
     * Serializes the entity to a JSON payload for the request.
     *
     * @param \Apigee\Edge\Entity\EntityInterface $entity
     *   Entity to be serialized.
     * @param array $context
     *   Context for the serializer.
     *
     * @return string
     *   JSON payload for the request.
     */
    protected function buildEntityCreatePayload(EntityInterface $entity, array $context = []): string
    {
        return $this->getEntitySerializer()->serialize($entity, 'json', $context);
    }
}
