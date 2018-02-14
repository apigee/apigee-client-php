<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\EntityDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class AppCredentialDenormalizer.
 */
class AppCredentialDenormalizer extends EntityDenormalizer implements DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return $type instanceof AppCredentialInterface || in_array(AppCredentialInterface::class, class_implements($type));
    }

    /**
     * @inheritdoc
     */
    protected function denormalizeObjectProperty(
        bool $isCollection,
        $data,
        string $class,
        string $format = null,
        array $context = []
    ) {
        if (\DateTimeImmutable::class == $class && $data === -1) {
            // Taking special care of -1 as a credential's expiresAt property value.
            // Currently we does not limit this workaround to only the mentioned field because as far as we know
            // only expiresAt could have -1 as its value.
            // @see \Apigee\Edge\Api\Management\Entity\AppCredential::$expiresAt
            return null;
        }

        return parent::denormalizeObjectProperty(
            $isCollection,
            $data,
            $class,
            $format,
            $context
        );
    }
}
