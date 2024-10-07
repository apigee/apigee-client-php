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

use Apigee\Edge\Api\Monetization\Entity\TermsAndConditions;
use Apigee\Edge\Api\Monetization\Entity\TermsAndConditionsInterface;
use Apigee\Edge\Tests\Api\Monetization\Controller\OrganizationProfileControllerAwareTestTrait;
use Apigee\Edge\Tests\Test\Utility\RandomGeneratorAwareTrait;
use DateTimeImmutable;

trait TermsAndConditionsEntityProviderTrait
{
    use RandomGeneratorAwareTrait;
    use OrganizationProfileControllerAwareTestTrait;

    protected static function getNewTnC(bool $randomData = true): TermsAndConditionsInterface
    {
        $entity = new TermsAndConditions([
            'startDate' => $randomData ? new DateTimeImmutable('tomorrow') : DateTimeImmutable::createFromFormat(TermsAndConditionsInterface::DATE_FORMAT, '2017-12-31 16:15:59'),
            'description' => $randomData ? static::randomGenerator()->text() : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            // In the API level the version could be anything, so it is string.
            // Because it is a string JSON float serialization trick does
            // not work on it.
            'version' => $randomData ? (string) static::randomGenerator()->number() : '1.0',
            'url' => $randomData ? static::randomGenerator()->url() : 'http://example.com',
            // Organization profile must be passed otherwise the normalizer
            // does not know how to fix the timezone of date properties.
            'organization' => static::organizationProfileController()->load(),
        ]);

        return $entity;
    }

    protected static function getUpdatedTnC(TermsAndConditionsInterface $existing, bool $randomData = true): TermsAndConditionsInterface
    {
        $updated = clone $existing;
        $updated->setDescription($randomData ? static::randomGenerator()->text() : 'Maecenas tristique vulputate leo porta sagittis.');
        $updated->setVersion($randomData ? (string) static::randomGenerator()->number() : '2.0');
        $updated->setUrl($randomData ? static::randomGenerator()->text() : 'http://foo.example.com');

        return $updated;
    }
}
