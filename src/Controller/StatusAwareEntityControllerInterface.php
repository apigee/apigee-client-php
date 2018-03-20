<?php

/**
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

namespace Apigee\Edge\Controller;

use Apigee\Edge\Entity\Property\StatusPropertyAwareTrait;

/**
 * Interface StatusAwareEntityControllerInterface.
 *
 * Entity controller for those entities that has "status" property and the value of that property (and with that the
 * status of the entity itself) can be changed only with an additional API call.
 *
 * @see https://docs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D
 * @see https://docs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/apps/%7Bapp_name%7D
 * @see StatusPropertyAwareTrait
 */
interface StatusAwareEntityControllerInterface
{
    public function setStatus(string $entityId, string $status): void;
}
