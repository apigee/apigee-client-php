<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Denormalizer;

use Apigee\Edge\Structure\CredentialProduct;
use Apigee\Edge\Structure\CredentialProductInterface;
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
        $type = rtrim($type, '[]');

        return $type instanceof CredentialProductInterface || in_array(CredentialProductInterface::class, class_implements($type));
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (is_array($data)) {
            $denormalized = [];
            $class = rtrim($class, '[]');
            foreach ($data as $item) {
                $denormalized[] = $this->denormalizeObject($item, $class, $format, $context);
            }

            return $denormalized;
        }

        return $this->denormalizeObject($data, $class, $format, $context);
    }

    private function denormalizeObject($data, $class, $format = null, array $context = [])
    {
        return new CredentialProduct($data->apiproduct, $data->status);
    }
}
