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

namespace Apigee\Edge\Tests\Api\Management\Entity;

use Apigee\Edge\Api\Management\Entity\ApiProduct;
use Apigee\Edge\Api\Management\Entity\ApiProductInterface;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Tests\Test\Utility\RandomGeneratorAwareTrait;

trait ApiProductTestEntityProviderTrait
{
    use RandomGeneratorAwareTrait;

    protected static function getNewApiProduct(bool $randomData = true): ApiProductInterface
    {
        $entity = new ApiProduct([
            'name' => $randomData ? static::randomGenerator()->machineName() : 'phpunit_test',
            'displayName' => $randomData ? static::randomGenerator()->displayName() : 'PHP Unit Test product',
            'approvalType' => ApiProductInterface::APPROVAL_TYPE_AUTO,
            'attributes' => new AttributesProperty(['foo' => 'bar']),
            'scopes' => ['scope 1', 'scope 2'],
            'quota' => $randomData ? static::randomGenerator()->number() : 10,
            'quotaInterval' => $randomData ? static::randomGenerator()->number() : 1,
            'quotaTimeUnit' => ApiProductInterface::QUOTA_INTERVAL_MINUTE,
        ]);

        return $entity;
    }

    protected static function getUpdatedApiProduct(ApiProductInterface $existing, bool $randomData = true): ApiProductInterface
    {
        $updated = clone $existing;
        $updated->setDisplayName($randomData ? static::randomGenerator()->displayName() : '(Edited) PHP Unit Test product');
        $updated->setApprovalType(ApiProductInterface::APPROVAL_TYPE_MANUAL);
        $updated->setAttributes(new AttributesProperty(['foo' => 'foo', 'bar' => 'baz']));
        $updated->setQuota($randomData ? static::randomGenerator()->number() : 1000);
        $updated->setQuotaInterval($randomData ? static::randomGenerator()->number() : 12);
        $updated->setQuotaTimeUnit(ApiProductInterface::QUOTA_INTERVAL_HOUR);

        return $updated;
    }
}
