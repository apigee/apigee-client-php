<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Structure;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class CredentialProductDenormalizer.
 */
class CredentialProductDenormalizer implements DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type instanceof CredentialProductInterface;
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return new CredentialProduct($data->apiproduct, $data->status);
    }
}
