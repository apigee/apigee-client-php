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

use Apigee\Edge\Api\Monetization\Structure\NestedObjectReferenceInterface;
use Apigee\Edge\Normalizer\ObjectNormalizer;
use ReflectionObject;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * Ensures Monetization related entities gets normalized properly.
 */
class EntityNormalizer extends ObjectNormalizer
{
    /**
     * Contains values of referenced entities. When a new entity is created
     * the related controller will pass the required referenced therefore the
     * developer do not need to set them on the entity manually.
     */
    public const MINT_ENTITY_REFERENCE_PROPERTY_VALUES = 'mint_entity_reference_values';

    /**
     * @var NameConverterInterface|null
     */
    protected $nameConverter;

    /**
     * EntityNormalizer constructor.
     *
     * @param ClassMetadataFactoryInterface|null $classMetadataFactory
     * @param NameConverterInterface|null $nameConverter
     * @param PropertyAccessorInterface|null $propertyAccessor
     * @param PropertyTypeExtractorInterface|null $propertyTypeExtractor
     */
    public function __construct(?ClassMetadataFactoryInterface $classMetadataFactory = null, ?NameConverterInterface $nameConverter = null, ?PropertyAccessorInterface $propertyAccessor = null, ?PropertyTypeExtractorInterface $propertyTypeExtractor = null)
    {
        $this->nameConverter = $nameConverter;
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor);
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress InvalidReturnType stdClass is also an object.
     * @psalm-suppress InvalidPropertyFetch.
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $normalized = (array) parent::normalize($object, $format, $context);

        $entityReferenceProperties = $this->getNestedObjectProperties($object);

        if (!empty($entityReferenceProperties)) {
            foreach ($entityReferenceProperties as $entityProperty => $normalizedProperty) {
                // Ensure we do not send the complete referenced entity object
                // only the referenced entity id.
                if (isset($normalized[$normalizedProperty]->apiProduct)) {
                    $normalized = [
                        'apiproduct' => $normalized[$normalizedProperty]->apiProduct,
                    ];
                } elseif (isset($normalized[$normalizedProperty]->id)) {
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

        return $this->convertToArrayObject($normalized);
    }

    /**
     * Returns a list of properties on an object that contain
     * references (nested objects) to another objects.
     *
     * In case of these properties there is no need to send the complete
     * nested object to the Monetization API when a new entity is
     * created or updated, it is enough to send only the id of the
     * referenced object. (Sending the id of the referenced object is
     * required.)
     *
     * It does not support array object properties, those should be handled
     * in child classes.
     *
     * @param object $object
     *
     * @return array
     *   An associative array of properties that contain nested object
     *   references. Array keys are the (internal) property names on an
     *   object and values are the normalized (external) property names that
     *   should be sent to Apigee Edge.
     */
    protected function getNestedObjectProperties($object): array
    {
        $entityReferenceProperties = [];
        $ro = new ReflectionObject($object);
        foreach ($ro->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($object);
            if (is_object($value) && $value instanceof NestedObjectReferenceInterface) {
                if ($this->nameConverter) {
                    $normalizedPropertyName = $this->nameConverter->normalize($property->getName());
                } else {
                    $normalizedPropertyName = $property->getName();
                }
                $entityReferenceProperties[$property->getName()] = $normalizedPropertyName;
            }
        }

        return $entityReferenceProperties;
    }
}
