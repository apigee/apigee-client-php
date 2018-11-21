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

namespace Apigee\Edge\Api\Monetization\Entity;

use Apigee\Edge\Api\Monetization\Entity\Property\OrganizationPropertyAwareTrait;

/**
 * Base class for those Monetization entities that contains reference to
 * the organization that it belongs.
 *
 * Known issue:
 *  * The organization profile data is incomplete in these entities, for example
 *    it does not contains the address of the organization even if it is set.
 */
abstract class OrganizationAwareEntity extends Entity implements OrganizationAwareEntityInterface
{
    use OrganizationPropertyAwareTrait;
}
