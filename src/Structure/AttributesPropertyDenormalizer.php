<?php

namespace Apigee\Edge\Structure;

/**
 * Class AttributesPropertyDenormalizer.
 *
 * @package Apigee\Edge\Structure
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class AttributesPropertyDenormalizer extends KeyValueMapDenormalizer
{
    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type instanceof AttributesProperty;
    }

    /**
     * Transforms JSON representation of attributes property to compatible with what Edge accepts.
     *
     * Example: $values = [['name' => 'foo', 'value' => 'bar'], ['name' => 'bar', 'value' => 'baz']].
     *
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $flatten = [];
        foreach ($data as $key => $item) {
            // Do not transform proper arrays. Ex.: ['foo' => 'bar', 'bar' => 'baz'].
            if (is_array($item)) {
                $flatten[$item['name']] = $item['value'];
            } else {
                $flatten[$key] = $item;
            }
        }
        $data = $flatten;
        return parent::denormalize($data, $class, $format, $context);
    }
}
