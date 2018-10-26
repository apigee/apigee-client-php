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

use Apigee\Edge\Api\Monetization\Entity\SupportedCurrency;
use Apigee\Edge\Api\Monetization\Entity\SupportedCurrencyInterface;
use Apigee\Edge\Tests\Test\Utility\RandomGeneratorAwareTrait;

trait SupportedCurrencyEntityProviderTrait
{
    use RandomGeneratorAwareTrait;

    protected static function getNewSupportedCurrency(bool $randomData = true): SupportedCurrencyInterface
    {
        $entity = new SupportedCurrency([
            'name' => $randomData ? static::randomGenerator()->machineName() : 'phpunit',
            'displayName' => $randomData ? static::randomGenerator()->displayName() : 'PHPUnit',
            'description' => $randomData ? static::randomGenerator()->text() : 'Test supported currency.',
            'minimumTopupAmount' => $randomData ? static::randomGenerator()->number() * 0.1 : 10.0,
            'creditLimit' => $randomData ? static::randomGenerator()->number() * 0.1 : 10.01,
            'virtualCurrency' => true,
        ]);

        return $entity;
    }

    protected static function getUpdatedSupportedCurrency(SupportedCurrencyInterface $existing, bool $randomData = true): SupportedCurrencyInterface
    {
        $updated = clone $existing;
        $updated->setDisplayName($randomData ? static::randomGenerator()->displayName() : '(edited) PHPUnit');
        $updated->setDescription($randomData ? static::randomGenerator()->text() : '(edited) Test supported currency.');

        return $updated;
    }
}
