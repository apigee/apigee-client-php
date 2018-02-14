<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Structure;

/**
 * Class PropertiesPropertyNormalizer.
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
