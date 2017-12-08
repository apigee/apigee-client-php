<?php

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
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class EntityDenormalizer implements DenormalizerInterface
{
    protected $propertyTypeExtractor;

    /**
     * EntityDenormalizer constructor.
     */
    public function __construct()
    {
        $reflectionExtractor = new ReflectionExtractor();
        $phpDocExtractor = new PhpDocExtractor();

        $this->propertyTypeExtractor = new PropertyInfoExtractor(
            [
                $reflectionExtractor,
                $phpDocExtractor
            ],
            // Type extractors
            [
                $phpDocExtractor,
                $reflectionExtractor
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

        return new $class($denormalized);
    }

    /**
     * Denormalizes an entity property.
     *
     * @param mixed $data
     *   Data to restore.
     * @param string $property
     *   Name of the property on class.
     * @param $class
     *   The expected class to instantiate.
     * @param string $format
     *   Format the given data was extracted from.
     * @param array $context
     *   Options available to the denormalizer.
     *
     * @return mixed
     */
    private function denormalizeProperty($data, $property, $class, string $format = null, array $context = [])
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
                $denormalized = $this
                    ->denormalizeObjectProperty($type->isCollection(), $data, $class, $format, $context);
            }
        }
        return $denormalized;
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
     * @return mixed
     */
    private function denormalizeObjectProperty(
        bool $isCollection,
        $data,
        string $class,
        string $format = null,
        array $context = []
    ) {
        $denormalized = $data;
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

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return in_array(Entity::class, class_parents($type));
    }
}
