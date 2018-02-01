<?php

namespace Apigee\Edge\Structure;

/**
 * Class PropertiesPropertyNormalizer.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class PropertiesPropertyNormalizer extends KeyValueMapNormalizer
{
    /**
     * Transforms JSON representation of properties property to compatible with what Edge accepts.
     *
     * @inheritdoc
     *
     * @psalm-suppress InvalidReturnType Returning an object here is required
     * for creating a valid Apigee Edge request.
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $return = [
            'property' => parent::normalize($object, $format, $context),
        ];

        return (object) $return;
    }
}
