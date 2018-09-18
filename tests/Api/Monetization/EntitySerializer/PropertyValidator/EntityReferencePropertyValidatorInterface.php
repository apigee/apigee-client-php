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

namespace Apigee\Edge\Tests\Api\Monetization\EntitySerializer\PropertyValidator;

/**
 * Validates serialization and deserialization of entity reference properties
 * that usually contains a complete entity object in the input but in the
 * output only the id of the referenced entity should be sent to Apigee Edge.
 *
 * After an entity reference property got validated it should be removed from
 * input and the output because as it is written above its content in the input
 * and in the output is intentionally different.
 */
interface EntityReferencePropertyValidatorInterface extends PropertyValidatorInterface
{
}
