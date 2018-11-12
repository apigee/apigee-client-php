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

namespace Apigee\Edge\Tests\Api\Monetization\Controller;

use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\RatePlanSerializerValidator;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerValidatorInterface;

/**
 * Base test class for developer- and company active rate plans.
 */
abstract class ActiveRatePlanControllerTestBase extends EntityControllerTestBase
{
    public function testGetEntities(): void
    {
        /** @var \Apigee\Edge\Api\Monetization\Controller\ActiveRatePlanControllerInterface $controller */
        $controller = static::entityController();
        /** @var \Apigee\Edge\Api\Monetization\Entity\RatePlanInterface[] $entities */
        $entities = $controller->getEntities();
        $json = json_decode((string) static::defaultAPIClient()->getJournal()->getLastResponse()->getBody());
        $json = reset($json);
        $i = 0;
        foreach ($entities as $entity) {
            $this->entitySerializerValidator()->validate($json[$i], $entity);
            ++$i;
        }
    }

    public function testGetActiveRatePlanByApiProduct(): void
    {
        /** @var \Apigee\Edge\Api\Monetization\Controller\ActiveRatePlanControllerInterface $controller */
        $controller = static::entityController();
        // This ensures the right API endpoint gets called and the API response
        // can be parsed.
        $controller->getActiveRatePlanByApiProduct('phpunit');
        // No query parameters.
        $this->assertEmpty(static::defaultAPIClient()->getJournal()->getLastRequest()->getUri()->getQuery());
        try {
            $controller->getActiveRatePlanByApiProduct('phpunit', true);
        } catch (\Exception $exception) {
            // File does not exist, so it is fine.
        }
        $this->assertEquals('showPrivate=true', static::defaultAPIClient()->getJournal()->getLastRequest()->getUri()->getQuery());
    }

    /**
     * @inheritDoc
     */
    protected function entitySerializerValidator(): EntitySerializerValidatorInterface
    {
        static $validator;
        if (null === $validator) {
            $validator = new RatePlanSerializerValidator($this->entitySerializer());
        }

        return $validator;
    }
}
