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

namespace Apigee\Edge\Api\Monetization\Controller;

use Apigee\Edge\Api\Monetization\Entity\EntityInterface;
use Apigee\Edge\Controller\ClientAwareControllerTrait;
use Apigee\Edge\Controller\EntityEndpointAwareControllerTrait;
use Apigee\Edge\Controller\EntitySerializerAwareTrait;

/**
 * Trait EntityUpdateOperationControllerTrait.
 *
 * @see \Apigee\Edge\Api\Monetization\Controller\EntityUpdateControllerOperationInterface
 */
trait EntityUpdateOperationControllerTrait
{
    use ClientAwareControllerTrait;
    use EntityEndpointAwareControllerTrait;
    use EntitySerializerAwareTrait;

    /**
     * @inheritdoc
     *
     * @psalm-suppress PossiblyNullArgument $entity->id() is always not null here.
     */
    public function update(EntityInterface $entity): void
    {
        $uri = $this->getEntityEndpointUri($entity->id());
        $payload = $this->getEntitySerializer()->serialize($entity, 'json');
        // Update an existing entity.
        $response = $this->getClient()->put($uri, $payload);
        $this->getEntitySerializer()->setPropertiesFromResponse($response, $entity);
    }
}
