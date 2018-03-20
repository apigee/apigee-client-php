<?php

/**
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

namespace Apigee\Edge\Entity;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class EntityDenormalizer.
 *
 * Denormalizes an entity from Apigee Edge's response to our internal structure.
 */
class EntityDenormalizer implements DenormalizerInterface
{
    protected $propertyTypeExtractor;

    /**
     * EntityDenormalizer constructor.
     *
     * @psalm-suppress InvalidArgument This can be removed when minimum symfony/property-info dependency changes to 3.3.
     *
     * @see https://github.com/symfony/property-info/commit/b7637b4afd31879461141a5fa0c7b40b08b46f2e
     */
    public function __construct()
    {
        $reflectionExtractor = new ReflectionExtractor();
        $phpDocExtractor = new PhpDocExtractor();

        $this->propertyTypeExtractor = new PropertyInfoExtractor(
            [
                $reflectionExtractor,
                $phpDocExtractor,
            ],
            // Type extractors
            [
                $phpDocExtractor,
                $reflectionExtractor,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $denormalized = [];
        foreach ($data as $key => $value) {
            $denormalized[$key] = $this->denormalizeProperty($value, $key, $class);
        }

        // Do not pass null values to constructions. Default values should be null where it is needed and setters
        // should allow passing null values only where it is actually acceptable.
        $denormalized = array_filter($denormalized, function ($value) {
            return !is_null($value);
        });

        return new $class($denormalized);
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type instanceof EntityInterface || in_array(EntityInterface::class, class_implements($type));
    }

    /**
     * @param bool $isCollection
     *   Indicates whether the data should be denormalized as collection of objects.
     * @param mixed $data
     *   Data to restore.
     * @param string $class
     *   The expected class to instantiate.
     * @param string $format
     *   Format the given data was extracted from.
     * @param array $context
     *   Options available to the denormalizer.
     *
     * @throws \ReflectionException
     *
     * @return mixed
     */
    protected function denormalizeObjectProperty(
        bool $isCollection,
        $data,
        string $class,
        string $format = null,
        array $context = []
    ) {
        $denormalized = $data;
        if (\DateTimeImmutable::class == $class) {
            // Reading dates from Unix epoch timestamps used by Apigee Edge.
            $denormalized = new \DateTimeImmutable('@' . intval($data / 1000));
        } else {
            $propertyDenormalizerClass = "{$class}Denormalizer";
            if (class_exists($propertyDenormalizerClass) &&
                in_array(DenormalizerInterface::class, class_implements($propertyDenormalizerClass))) {
                $rc = new \ReflectionClass($propertyDenormalizerClass);
                // Initialize a new object instead of calling this function in static.
                $propertyDenormalizer = $rc->newInstance();
                if ($isCollection) {
                    foreach ($data as $key => $value) {
                        $denormalized[$key] =
                            call_user_func([$propertyDenormalizer, 'denormalize'], $value, $class, $format, $context);
                    }
                } else {
                    $denormalized =
                        call_user_func([$propertyDenormalizer, 'denormalize'], $data, $class, $format, $context);
                }
            }
        }

        return $denormalized;
    }

    /**
     * Denormalizes an entity property.
     *
     * @param mixed $data
     *   Data to restore.
     * @param string $property
     *   Name of the property on class.
     * @param string $class
     *   The expected class to instantiate.
     * @param string $format
     *   Format the given data was extracted from.
     * @param array $context
     *   Options available to the denormalizer.
     *
     * @return mixed
     */
    private function denormalizeProperty($data, string $property, string $class, string $format = null, array $context = [])
    {
        if (null === $types = $this->propertyTypeExtractor->getTypes($class, $property)) {
            return $data;
        }
        $denormalized = $data;
        /** @var \Symfony\Component\PropertyInfo\Type[] $types */
        foreach ($types as $type) {
            if (null === $data && $type->isNullable()) {
                return $data;
            }

            list('builtInType' => $builtInType, 'class' => $class, 'collectionKeyType' => $collectionKeyType) =
                $this->getPropertyTypeInfo($type);

            if (null !== $collectionKeyType) {
                $context['key_type'] = $collectionKeyType;
            }

            if (Type::BUILTIN_TYPE_OBJECT === $builtInType) {
                try {
                    $denormalized = $this
                        ->denormalizeObjectProperty($type->isCollection(), $data, $class, $format, $context);
                } catch (\ReflectionException $e) {
                }
            }
        }

        return $denormalized;
    }

    /**
     * @param \Symfony\Component\PropertyInfo\Type $type
     *
     * @return array
     */
    private function getPropertyTypeInfo(Type $type): array
    {
        $builtinType = $type->getBuiltinType();
        $class = $type->getClassName();
        $collectionKeyType = null;
        if ($type->isCollection() && null !== ($collectionValueType = $type->getCollectionValueType())
            && Type::BUILTIN_TYPE_OBJECT === $collectionValueType->getBuiltinType()) {
            $builtinType = Type::BUILTIN_TYPE_OBJECT;
            $class = $collectionValueType->getClassName();
            $collectionKeyType = $type->getCollectionKeyType();
        }

        return [
            'builtInType' => $builtinType,
            'class' => $class,
            'collectionKeyType' => $collectionKeyType,
        ];
    }
}
