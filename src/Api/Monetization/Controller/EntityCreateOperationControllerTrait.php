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
use Apigee\Edge\Api\Monetization\Entity\OrganizationAwareEntity;
use Apigee\Edge\Api\Monetization\Normalizer\EntityNormalizer;
use Apigee\Edge\Controller\ClientAwareControllerTrait;
use Apigee\Edge\Controller\EntitySerializerAwareTrait;
use Apigee\Edge\Controller\OrganizationAwareControllerTrait;

/**
 * Trait EntityCreateOperationControllerTrait.
 *
 * @see \Apigee\Edge\Api\Monetization\Controller\EntityCreateOperationInterface
 */
trait EntityCreateOperationControllerTrait
{
    use ClientAwareControllerTrait;
    use OrganizationAwareControllerTrait;
    use EntitySerializerAwareTrait;

    /**
     * @inheritdoc
     */
    public function create(EntityInterface $entity): void
    {
        $payload = $this->getEntitySerializer()->serialize($entity, 'json', $this->buildContextForEntityTransformerInCreate($entity));
        $response = $this->getClient()->post($this->getBaseEndpointUri(), $payload);
        $this->getEntitySerializer()->setPropertiesFromResponse($response, $entity);
    }

    /**
     * Builds context for the entity normalizer.
     *
     * Allows controllers to add extra metadata to the payload. Ex.: to be able
     * to create a rate plan you do not need to load the API package and pass
     * it to the rate plan's constructor, it is enough to save the created
     * rate plan entity with the rate plan controller that will add the package
     * id to the request payload.
     *
     * @param \Apigee\Edge\Api\Monetization\Entity\EntityInterface $entity
     *   Entity to be created.
     *
     * @return array
     */
    protected function buildContextForEntityTransformerInCreate(EntityInterface $entity): array
    {
        $context = [];
        if ($entity instanceof OrganizationAwareEntity) {
            $context[EntityNormalizer::MINT_ENTITY_REFERENCE_PROPERTY_VALUES]['organization'] = $this->getOrganisationName();
        }

        return $context;
    }
}
