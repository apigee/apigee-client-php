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

namespace Apigee\Edge\Api\Monetization\Denormalizer;

use Apigee\Edge\Api\Monetization\Entity\Company;
use Apigee\Edge\Api\Monetization\Entity\Developer;
use Apigee\Edge\Api\Monetization\Entity\LegalEntityInterface;

/**
 * Dynamically denormalizes legal entities to developers or companies.
 */
class LegalEntityDenormalizer extends EntityDenormalizer
{
    /**
     * Fully qualified class name of the developer entity.
     *
     * @var string
     */
    protected $developerClass = Developer::class;

    /**
     * Fully qualified class name of the company entity.
     *
     * @var string
     */
    protected $companyClass = Company::class;

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if ($data->isCompany) {
            return parent::denormalize($data, $this->companyClass, $format, $context);
        } else {
            return parent::denormalize($data, $this->developerClass, $format, $context);
        }
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

        return LegalEntityInterface::class === $type || $type instanceof LegalEntityInterface || in_array(LegalEntityInterface::class, class_implements($type));
    }
}
