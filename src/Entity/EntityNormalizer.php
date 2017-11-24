<?php

namespace Apigee\Edge\Entity;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class EntityNormalizer.
 *
 * Normalizes entity data to Apigee Edge's format.
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class EntityNormalizer implements NormalizerInterface, DenormalizerInterface
{
    protected $propertyTypeExtractor;

    /**
     * EntityNormalizer constructor.
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
    public function normalize($object, $format = null, array $context = [])
    {
        $asArray = [];
        $ro = new \ReflectionObject($object);
        foreach ($ro->getProperties() as $property) {
            $getter = 'get' . ucfirst($property->getName());
            if ($ro->hasMethod($getter)) {
                $value = call_user_func([$object, $getter]);
                if (is_object($value)) {
                    $class = get_class($value);
                    $propertyNormalizerClass = "{$class}Normalizer";
                    if (class_exists($propertyNormalizerClass) &&
                        in_array(NormalizerInterface::class, class_implements($propertyNormalizerClass))) {
                        $value = call_user_func([$propertyNormalizerClass, 'normalize'], $value, $format, $context);
                    }
                }
                $asArray[$property->getName()] = $value;
            }
        }
        // Exclude empty values from the output, even if PATCH is not supported on Apigee Edge
        // sending a smaller portion of data in POST/PUT is always a good practice.
        $asArray = array_filter($asArray);
        ksort($asArray);
        return (object)$asArray;
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof EntityInterface;
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
     * @inheritdoc
     */
    protected function denormalizeProperty($data, $attribute, $class, $format = null, array $context = [])
    {
        if (null === $types = $this->propertyTypeExtractor->getTypes($class, $attribute)) {
            return $data;
        }
        $denormalized = $data;
        /** @var \Symfony\Component\PropertyInfo\Type[] $types */
        foreach ($types as $type) {
            if (null === $data && $type->isNullable()) {
                return $data;
            }

            if ($type->isCollection() && null !== ($collectionValueType = $type->getCollectionValueType())
                && Type::BUILTIN_TYPE_OBJECT === $collectionValueType->getBuiltinType()) {
                $builtinType = Type::BUILTIN_TYPE_OBJECT;
                $class = $collectionValueType->getClassName();

                if (null !== $collectionKeyType = $type->getCollectionKeyType()) {
                    $context['key_type'] = $collectionKeyType;
                }
            } else {
                $builtinType = $type->getBuiltinType();
                $class = $type->getClassName();
            }

            if (Type::BUILTIN_TYPE_OBJECT === $builtinType) {
                $propertyNormalizerClass = "{$class}Normalizer";
                if (class_exists($propertyNormalizerClass) &&
                    in_array(DenormalizerInterface::class, class_implements($propertyNormalizerClass))) {
                    $rc = new \ReflectionClass($propertyNormalizerClass);
                    // Initialize a new object instead of calling this function in static.
                    $propertyNormalizer = $rc->newInstance();
                    if ($type->isCollection()) {
                        foreach ($data as $key => $value) {
                            $denormalized[$key] =
                                call_user_func([$propertyNormalizer, 'denormalize'], $value, $class, $format, $context);
                        }
                    } else {
                        $denormalized =
                            call_user_func([$propertyNormalizer, 'denormalize'], $data, $class, $format, $context);
                    }
                }
            }
        }
        return $denormalized;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return class_exists($type);
    }
}
