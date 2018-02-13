<?php

namespace Apigee\Edge\Entity;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class EntityNormalizer.
 *
 * Normalizes entity data to Apigee Edge's format.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class EntityNormalizer implements NormalizerInterface
{
    /** @var \Symfony\Component\PropertyInfo\PropertyInfoExtractor */
    private $propertyTypeExtractor;

    /**
     * EntityNormalizer constructor.
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
     *
     * @psalm-suppress RedundantCondition !is_null() is not redundant in array_filter().
     * @psalm-suppress InvalidReturnType Returning an object here is required
     * for creating a valid Apigee Edge request.
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $asArray = [];
        $ro = new \ReflectionObject($object);
        $class = get_class($object);
        foreach ($ro->getProperties() as $property) {
            $getter = 'get' . ucfirst($property->getName());
            if (!$ro->hasMethod($getter)) {
                $getter = 'is' . ucfirst($property->getName());
            }
            if ($ro->hasMethod($getter)) {
                $asArray[$property->getName()] = $this->normalizeProperty(
                    call_user_func([$object, $getter]),
                    $property->getName(),
                    $class,
                    $format,
                    $context
                );
            }
        }
        // Exclude null values from the output, even if PATCH is not supported on Apigee Edge
        // sending a smaller portion of data in POST/PUT is always a good practice.
        $asArray = array_filter($asArray, function ($value) {
            return !is_null($value);
        });
        ksort($asArray);

        return (object) $asArray;
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof EntityInterface;
    }

    /**
     * Normalizes object value into a set of arrays and scalars.
     *
     * @param mixed $data
     *   Object property value to normalize.
     * @param string $property
     * @param string $class
     * @param string $format
     *   Format the normalization result will be encoded as
     * @param array $context
     *   Context options for the normalizer
     *
     * @throws \ReflectionException
     *
     * @return mixed
     */
    protected function normalizeProperty($data, string $property, string $class, $format, array $context = [])
    {
        if (null === $types = $this->propertyTypeExtractor->getTypes($class, $property)) {
            return $data;
        }
        $normalized = $data;
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
                    $normalized = $this
                        ->normalizeObjectProperty($type->isCollection(), $data, $class, $format, $context);
                } catch (\ReflectionException $e) {
                }
            } elseif (Type::BUILTIN_TYPE_ARRAY === $builtInType) {
                foreach ($data as $key => $item) {
                    if (is_object($item) || is_array($item)) {
                        $data[$key] = $this->normalizeProperty($item, $property, $class, $format, $context);
                    }
                }
            }
        }

        return $normalized;
    }

    /**
     * @param $isCollection
     * @param $data
     * @param $class
     * @param $format
     * @param $context
     *
     * @throws \ReflectionException
     *
     * @return mixed
     */
    protected function normalizeObjectProperty($isCollection, $data, $class, $format, $context)
    {
        $normalized = $data;
        if (\DateTimeImmutable::class == $class && null !== $data) {
            /** @var \DateTimeImmutable $data */
            // Transforming timestamps to Unix epoch timestamps used by Apigee Edge.
            $normalized = $data->getTimestamp() * 1000;
        } else {
            $propertyNormalizerClass = "{$class}Normalizer";
            if (class_exists($propertyNormalizerClass) &&
                in_array(NormalizerInterface::class, class_implements($propertyNormalizerClass))) {
                $rc = new \ReflectionClass($propertyNormalizerClass);
                // Initialize a new object instead of calling this function in static.
                $propertyNormalizer = $rc->newInstance();
                if ($isCollection) {
                    foreach ($data as $key => $value) {
                        $normalized[$key] =
                            call_user_func([$propertyNormalizer, 'normalize'], $value, $format, $context);
                    }
                } else {
                    $normalized =
                        call_user_func([$propertyNormalizer, 'normalize'], $data, $format, $context);
                }
            }
        }

        return $normalized;
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
