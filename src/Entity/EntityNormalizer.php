<?php

namespace Apigee\Edge\Entity;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
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
        $json = [];
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
                $json[$property->getName()] = $value;
            }
        }
        // Exclude empty values from the output, even if PATCH is not supported on Apigee Edge
        // sending a smaller portion of data in POST/PUT is always a good practice.
        $json = array_filter($json);
        ksort($json);
        return (object)$json;
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed $data Data to normalize
     * @param string $format The format being (de-)serialized from or into
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof EntityInterface;
    }

    /**
     * Denormalizes data back into an object of the given class.
     *
     * @param mixed $data Data to restore
     * @param string $class The expected class to instantiate
     * @param string $format Format the given data was extracted from
     * @param array $context Options available to the denormalizer
     *
     * @return object
     *
     * @throws BadMethodCallException   Occurs when the normalizer is not called in an expected context
     * @throws InvalidArgumentException Occurs when the arguments are not coherent or not supported
     * @throws UnexpectedValueException Occurs when the item cannot be hydrated with the given data
     * @throws ExtraAttributesException Occurs when the item doesn't have attribute to receive given data
     * @throws LogicException           Occurs when the normalizer is not supposed to denormalize
     * @throws RuntimeException         Occurs if the class cannot be instantiated
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $denormalized = [];
        foreach ($data as $key => $value) {
            $denormalized[$key] = $this->denormalizeProperty($value, $key, $class);
        }

        return new $class($denormalized);
    }

    protected function denormalizeProperty($data, $attribute, $class, $format = null, array $context = [])
    {
        if (null === $this->propertyTypeExtractor ||
            null === $types = $this->propertyTypeExtractor->getTypes($class, $attribute)) {
            return $data;
        }
        /** @var \Symfony\Component\PropertyInfo\Type[] $types */
        foreach ($types as $type) {
            if (null === $data && $type->isNullable()) {
                return $data;
            }
            $builtinType = $type->getBuiltinType();
            $class = $type->getClassName();
            if (Type::BUILTIN_TYPE_OBJECT === $builtinType) {
                $propertyNormalizerClass = "{$class}Normalizer";
                if (class_exists($propertyNormalizerClass) &&
                    in_array(DenormalizerInterface::class, class_implements($propertyNormalizerClass))) {
                    $data = call_user_func([$propertyNormalizerClass, 'denormalize'], $data, $class, $format, $context);
                }
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return class_exists($type);
    }
}
