<?php

namespace Apigee\Edge\Structure;

/**
 * Class AttributesPropertyDenormalizer.
 *
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
     * Transforms input data to our internal representation.
     *
     * Acceptable inputs:
     *   - Edge response format: $data = [{'name' => 'foo', 'value' => 'bar'}, {'name' => 'bar', 'value' => 'baz'}]
     *     (from the EntityNormalizer for example)
     *   - Internal representation of attributes: ['foo' => 'bar', 'bar' => 'baz']
     *
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $flatten = [];
        foreach ($data as $key => $item) {
            if (is_object($item)) {
                // $data came from the EntityNormalizer.
                $flatten[$item->name] = $item->value;
            } else {
                $flatten[$key] = $item;
            }
        }
        $data = $flatten;

        return parent::denormalize($data, $class, $format, $context);
    }
}
