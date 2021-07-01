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

namespace Apigee\Edge\Tests\Api\Management\EntitySerializer;

use Apigee\Edge\Entity\EntityInterface;
use PHPUnit\Framework\Assert;

class AppSerializerValidator extends EntitySerializerValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate(\stdClass $input, EntityInterface $entity): void
    {
        /** @var \Apigee\Edge\Api\Management\Entity\AppInterface $entity */
        /** @var \Apigee\Edge\Api\Management\Entity\AppCredentialInterface $credential */
        // We have to validate issued at date separately because we do not
        // store the milliseconds.
        $credentials = $entity->getCredentials();
        $credential = reset($credentials);
        Assert::assertEquals(intval($input->credentials[0]->issuedAt / 1000), $credential->getIssuedAt()->getTimestamp());
        $input->credentials[0]->issuedAt = $credential->getIssuedAt()->getTimestamp() * 1000;
        parent::validate($input, $entity);
    }
}
