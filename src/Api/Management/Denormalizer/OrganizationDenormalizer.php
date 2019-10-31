<?php

/*
 * Copyright 2019 Google LLC
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

namespace Apigee\Edge\Api\Management\Denormalizer;

use Apigee\Edge\Api\Management\Entity\HybridOrganization;
use Apigee\Edge\Api\Management\Entity\Organization;
use Apigee\Edge\Api\Management\Entity\OrganizationInterface;
use Apigee\Edge\Denormalizer\ObjectDenormalizer;

/**
 * Dynamically denormalizes organizations to organization or hybrid organization.
 */
class OrganizationDenormalizer extends ObjectDenormalizer
{
    /**
     * Fully qualified class name of the organization entity.
     *
     * @var string
     */
    protected $orgClass = Organization::class;

    /**
     * Fully qualified class name of the hybrid organization entity.
     *
     * @var string
     */
    protected $hybridOrgClass = HybridOrganization::class;

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        // Check for the property "features.hybrid.enabled".
        if (!empty($data->properties->property) && is_array($data->properties->property)) {
            foreach ($data->properties->property as $property) {
                if ('features.hybrid.enabled' == $property->name && 'TRUE' == strtoupper($property->value)) {
                    return parent::denormalize($data, $this->hybridOrgClass, $format, $context);
                }
            }
        }

        return parent::denormalize($data, $this->orgClass, $format, $context);
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        // Do not apply this on array objects. ArrayDenormalizer takes care of them.
        if ('[]' === substr($type, -2)) {
            return false;
        }

        return OrganizationInterface::class === $type || $type instanceof OrganizationInterface || in_array(OrganizationInterface::class, class_implements($type));
    }
}
