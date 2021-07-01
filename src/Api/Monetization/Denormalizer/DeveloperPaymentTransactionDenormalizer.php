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

use Apigee\Edge\Api\Monetization\Structure\DeveloperPaymentTransaction;

class DeveloperPaymentTransactionDenormalizer extends PaymentTransactionDenormalizer
{
    /**
     * Fully qualified class name of the developer payment transaction object.
     *
     * @var string
     */
    protected $developerPaymentTransactionClass = DeveloperPaymentTransaction::class;

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return parent::denormalize($data, $this->developerPaymentTransactionClass, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        if (parent::supportsDenormalization($data, $type, $format)) {
            return !$data->developer->isCompany;
        }

        return false;
    }
}
