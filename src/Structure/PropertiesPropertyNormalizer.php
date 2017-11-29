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
}
