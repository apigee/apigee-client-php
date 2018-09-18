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

namespace Apigee\Edge\Api\Monetization\Normalizer;

use Apigee\Edge\Api\Monetization\Entity\Entity;
use Apigee\Edge\Api\Monetization\Entity\EntityInterface;
use Apigee\Edge\Api\Monetization\Entity\Property\IdPropertyInterface;
use Apigee\Edge\Normalizer\EntityNormalizer as BaseEntityNormalizer;

/**
 * Ensures Monetization related entities gets normalized properly.
 */
class EntityNormalizer extends BaseEntityNormalizer
{
    /**
     * Contains values of referenced entities. When a new entity is created
     * the related controller will pass the required referenced therefore the
     * developer do not need to set them on the entity manually.
     */
    public const MINT_ENTITY_REFERENCE_PROPERTY_VALUES = 'mint_entity_reference_values';

    /**
     * @inheritdoc
     *
     * @psalm-suppress InvalidReturnType stdClass is also an object.
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $normalized = (array) parent::normalize($object, $format, $context);

        $entityReferenceProperties = $this->getEntityReferenceProperties($object);

        if (!empty($entityReferenceProperties)) {
            foreach ($entityReferenceProperties as $entityProperty => $normalizedProperty) {
                // Ensure we do not send the complete referenced entity object
                // only the referenced entity id.
                if (isset($normalized[$normalizedProperty]->id)) {
                    $normalized[$normalizedProperty] = [
                        'id' => $normalized[$normalizedProperty]->id,
                    ];
                }
            }
        }

        // Set missing ids of referenced entities passed by controllers.
        // Ex.: in case of entity create.
        // @see \Apigee\Edge\Api\Monetization\Controller\EntityCreateOperationControllerTrait::buildContextForEntityTransformerInCreate()
        if (!empty($context[self::MINT_ENTITY_REFERENCE_PROPERTY_VALUES])) {
            foreach ($context[self::MINT_ENTITY_REFERENCE_PROPERTY_VALUES] as $entityProperty => $value) {
                if (!isset($normalized[$entityProperty]['id'])) {
                    $normalized[$entityProperty] = [
                        'id' => $value,
                    ];
                }
            }
        }

        return (object) $normalized;
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return  $data instanceof EntityInterface && parent::supportsNormalization($data, $format);
    }

    /**
     * Return list of properties on the entity objects that contain references
     * (nested objects) to an other monetization entity.
     *
     * In case of these properties there is no need to send the complete
     * nested entity object to the Monetization API when a new entity is
     * created or updated, it is enough to send only the id of the
     * referenced entity. (Sending the id of the referenced entity is
     * required.)
     *
     * It does not support array object properties, those should be handled
     * in child classes.
     *
     * @param \Apigee\Edge\Api\Monetization\Entity\Entity $entity
     *
     * @return array
     *   An associative array of properties that contain nested entity
     *   references. Array keys are the (internal) property names on an entity
     *   object and values are the normalized (external) property names that
     *   should be sent to Apigee Edge.
     */
    protected function getEntityReferenceProperties(Entity $entity): array
    {
        $entityReferenceProperties = [];
        $ro = new \ReflectionObject($entity);
        foreach ($ro->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($entity);
            if (is_object($value)) {
                $rpo = new \ReflectionObject($value);
                // Anything not in the Entity namespace should be sent as-is
                // even if it implements the IdPropertyInterface.
                if ('Apigee\Edge\Api\Monetization\Entity' === $rpo->getNamespaceName() && $rpo->implementsInterface(IdPropertyInterface::class)) {
                    if ($this->nameConverter) {
                        $normalizedPropertyName = $this->nameConverter->normalize($property->getName());
                    } else {
                        $normalizedPropertyName = $property->getName();
                    }
                    $entityReferenceProperties[$property->getName()] = $normalizedPropertyName;
                }
            }
        }

        return $entityReferenceProperties;
    }
}
