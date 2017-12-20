<?php

namespace Apigee\Edge\Structure;

/**
 * Class PropertiesPropertyDenormalizer.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class PropertiesPropertyDenormalizer extends KeyValueMapDenormalizer
{
    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof PropertiesProperty;
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (property_exists($data, 'property') && is_array($data->property)) {
            $flatten = [];
            foreach ($data->property as $property) {
                $flatten[$property->name] = $property->value;
            }
            $data = $flatten;
        }

        return parent::denormalize($data, $class, $format, $context);
    }
}
