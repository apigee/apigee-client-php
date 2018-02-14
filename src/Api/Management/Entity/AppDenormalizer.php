<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\EntityDenormalizer;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class AppDenormalizer.
 */
class AppDenormalizer extends EntityDenormalizer implements DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data->developerId)) {
            return parent::denormalize($data, DeveloperApp::class);
        } elseif (isset($data->companyName)) {
            return parent::denormalize($data, CompanyApp::class);
        }
        throw new UnexpectedValueException(
            'Unable to denormalize because both "developerId" and "companyName" are missing from data.'
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return AppInterface::class === $type || $type instanceof AppInterface || in_array($type, class_implements(AppInterface::class));
    }
}
