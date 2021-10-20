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

use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Api\Monetization\EntitySerializer\LegalEntitySerializerValidator;
use Apigee\Edge\Tests\Test\EntitySerializer\EntitySerializerValidatorInterface;

/**
 * Base class for validating developer- and company controllers.
 */
abstract class LegalEntityControllerTestBase extends EntityControllerTestBase
{
    use EntityLoadOperationControllerTestTrait {
        testLoad as private traitTestLoad;
    }

    /**
     * {@inheritdoc}
     */
    public function testLoad($created = null): EntityInterface
    {
        return $this->traitTestLoad($this->getTestEntityId());
    }

    /**
     * {@inheritdoc}
     */
    protected function entitySerializerValidator(): EntitySerializerValidatorInterface
    {
        static $validator;
        if (null === $validator) {
            $validator = new LegalEntitySerializerValidator($this->entitySerializer());
        }

        return $validator;
    }

    protected function getTestEntityId(): string
    {
        return 'phpunit';
    }
}
