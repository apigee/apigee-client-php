<?php

namespace Apigee\Edge\Structure;

/**
 * Class PropertiesPropertyDenormalizer.
 *
 * @package Apigee\Edge\Structure
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
        if (array_key_exists('property', $data) && is_array($data['property'])) {
            $flatten = [];
            foreach ($data['property'] as $value) {
                $flatten[$value['name']] = $value['value'];
            }
            $data = $flatten;
        }
        return parent::denormalize($data, $class, $format, $context);
    }
}
