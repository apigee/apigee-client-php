<?php

namespace Apigee\Edge\Entity;

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
                        $rc = new \ReflectionClass($propertyNormalizerClass);
                        // Initialize a new object instead of calling this function in static.
                        $propertyNormalizer = $rc->newInstance();
                        $value = call_user_func([$propertyNormalizer, 'normalize'], $value, $format, $context);
                    }
                }
                $asArray[$property->getName()] = $value;
            }
        }
        // Exclude empty values from the output, even if PATCH is not supported on Apigee Edge
        // sending a smaller portion of data in POST/PUT is always a good practice.
        $asArray = array_filter($asArray);
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
}
