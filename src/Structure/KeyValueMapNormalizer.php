<?php

namespace Apigee\Edge\Structure;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class KeyValueMapNormalizer.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class KeyValueMapNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $return = [];
        foreach ($object->values() as $key => $value) {
            $return[] = (object) ['name' => $key, 'value' => $value];
        }

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof KeyValueMapInterface;
    }
}
