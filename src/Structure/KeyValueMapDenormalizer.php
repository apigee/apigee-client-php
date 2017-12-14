<?php

namespace Apigee\Edge\Structure;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class KeyValueMapDenormalizer.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class KeyValueMapDenormalizer implements DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return new $class($data);
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type instanceof KeyValueMapInterface;
    }
}
