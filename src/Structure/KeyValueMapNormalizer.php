<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Structure;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class KeyValueMapNormalizer.
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
