<?php

/*
 * Copyright 2021 Google LLC
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

namespace Apigee\Edge\Api\ApigeeX\Controller;

use Apigee\Edge\Api\ApigeeX\Entity\BillingTypeInterface;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Controller\EntityLoadOperationControllerInterface;

/**
 * Interface BillingTypeControllerInterface.
 *
 * TODO: Add reference documentation link
 */
interface BillingTypeControllerInterface extends
    EntityControllerInterface,
    EntityLoadOperationControllerInterface
{

    /**
     * Gets the billingtype.
     *
     * @return \Apigee\Edge\Api\ApigeeX\Entity\BillingTypeInterface[]
     */
    public function getAllBillingDetails(): array;

    /**
     * Update the billing type of the developer.
     *
     * @param $billingtype
     *   Billing type that is switched to.
     */
    public function updateBillingType($billingtype): BillingTypeInterface;
}
