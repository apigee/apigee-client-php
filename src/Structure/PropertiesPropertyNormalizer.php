<?php

namespace Apigee\Edge\Structure;

/**
 * Class PropertiesPropertyNormalizer.
 *
 * @package Apigee\Edge\Structure
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class PropertiesPropertyNormalizer extends KeyValueMapNormalizer
{
    /**
     * Transforms JSON representation of properties property to compatible with what Edge accepts.
     *
     * @inheritdoc
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $return = [
            'property' => parent::normalize($object, $format, $context),
        ];
        return $return;
    }

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
