<?php

/*
 * Copyright 2018 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Denormalizer\EntityDenormalizer;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * Class AppDenormalizer.
 */
class AppDenormalizer extends EntityDenormalizer
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
        // Do not apply this on array objects. ArrayDenormalizer takes care of them.
        if ('[]' === substr($type, -2)) {
            return false;
        }

        return AppInterface::class === $type || $type instanceof AppInterface || in_array($type, class_implements(AppInterface::class));
    }
}
