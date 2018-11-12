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

use Apigee\Edge\Api\Monetization\Entity\OrganizationAwareEntity;
use Apigee\Edge\Api\Monetization\Normalizer\EntityNormalizer;
use Apigee\Edge\Controller\ClientAwareControllerTrait;
use Apigee\Edge\Controller\EntityCreateOperationControllerTrait as BaseEntityCreateOperationControllerTrait;
use Apigee\Edge\Controller\EntitySerializerAwareTrait;
use Apigee\Edge\Controller\OrganizationAwareControllerTrait;
use Apigee\Edge\Entity\EntityInterface;

/**
 * Trait EntityCreateOperationControllerTrait.
 *
 * @see \Apigee\Edge\Controller\EntityCreateOperationControllerInterface
 */
trait EntityCreateOperationControllerTrait
{
    use ClientAwareControllerTrait;
    use OrganizationAwareControllerTrait;
    use EntitySerializerAwareTrait;
    use BaseEntityCreateOperationControllerTrait {
        buildEntityCreatePayload as private baseBuildEntityCreatePayload;
    }

    /**
     * @inheritDoc
     */
    protected function buildEntityCreatePayload(EntityInterface $entity, array $context = []): string
    {
        if ($entity instanceof OrganizationAwareEntity) {
            $context[EntityNormalizer::MINT_ENTITY_REFERENCE_PROPERTY_VALUES]['organization'] = $this->getOrganisationName();
        }

        return $this->baseBuildEntityCreatePayload($entity, $context);
    }
}
