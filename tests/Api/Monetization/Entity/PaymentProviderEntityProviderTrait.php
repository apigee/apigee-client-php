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

namespace Apigee\Edge\Tests\Api\Monetization\Entity;

use Apigee\Edge\Api\Monetization\Entity\PaymentProvider;
use Apigee\Edge\Api\Monetization\Entity\PaymentProviderInterface;
use Apigee\Edge\Tests\Test\Utility\RandomGeneratorAwareTrait;

trait PaymentProviderEntityProviderTrait
{
    use RandomGeneratorAwareTrait;

    protected static function getNewPaymentProvider(bool $randomData = true): PaymentProviderInterface
    {
        $entity = new PaymentProvider([
            'authType' => $randomData ? static::randomGenerator()->machineName() : 'auth-type',
            'name' => $randomData ? static::randomGenerator()->machineName() : 'PHPUnit',
            'merchantCode' => $randomData ? static::randomGenerator()->displayName() : 'merchant-code',
            'description' => $randomData ? static::randomGenerator()->text() : 'test payment provider',
            'endpoint' => $randomData ? static::randomGenerator()->url() : 'http://example.com/api',
            'credential' => $randomData ? static::randomGenerator()->machineName() : 'credential',
        ]);

        return $entity;
    }

    protected static function getUpdatedPaymentProvider(PaymentProviderInterface $existing, bool $randomData = true): PaymentProviderInterface
    {
        $updated = clone $existing;
        $updated->setEndpoint($randomData ? static::randomGenerator()->displayName() : 'http://foo.example.com/api');
        $updated->setDescription($randomData ? static::randomGenerator()->text() : '(edited) test payment provider');

        return $updated;
    }
}
