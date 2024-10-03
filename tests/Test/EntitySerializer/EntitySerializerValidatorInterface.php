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

namespace Apigee\Edge\Tests\Test\EntitySerializer;

use Apigee\Edge\Entity\EntityInterface;
use stdClass;

interface EntitySerializerValidatorInterface
{
    /**
     * Validates entity objects created from API responses.
     *
     * @param stdClass $input
     *   JSON decoded raw API response as object.
     * @param EntityInterface $entity
     *   Created entity object from the API response by a serializer.
     */
    public function validate(stdClass $input, EntityInterface $entity): void;
}
