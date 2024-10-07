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

namespace Apigee\Edge\Api\Monetization\Controller;

use Apigee\Edge\Api\Monetization\Structure\LegalEntityTermsAndConditionsHistoryItem;
use Apigee\Edge\Controller\OrganizationAwareControllerInterface;

/**
 * Interface LegalEntityTermsAndConditionsControllerInterface.
 *
 * @see https://apidocs.apigee.com/api-reference/content/monetization-apis#terms-and-conditions
 * @see https://docs.apigee.com/api-platform/monetization/specify-terms-and-conditions#getacceptedtcapi
 */
interface LegalEntityTermsAndConditionsControllerInterface extends OrganizationAwareControllerInterface
{
    /**
     * Gets all accepted/declined terms and conditions history events
     * for a developer -or company.
     *
     * @return \Apigee\Edge\Api\Monetization\Structure\LegalEntityTermsAndConditionsHistoryItem[]
     */
    public function getTermsAndConditionsHistory(): array;

    /**
     * Accepts a terms and conditions by its id.
     *
     * Specifying audit date is omitted at this moment.
     *
     * @see \Apigee\Edge\Api\Monetization\Structure\LegalEntityTermsAndConditionsHistoryItem::$auditDate
     *
     * @param string $tncId
     *   Id of a terms and conditions.
     *
     * @return LegalEntityTermsAndConditionsHistoryItem
     */
    public function acceptTermsAndConditionsById(string $tncId): LegalEntityTermsAndConditionsHistoryItem;

    /**
     * Declines a terms and conditions by its id.
     *
     * Specifying audit date is omitted at this moment.
     *
     * @see \Apigee\Edge\Api\Monetization\Structure\LegalEntityTermsAndConditionsHistoryItem::$auditDate
     *
     * @param string $tncId
     *   Id of a terms and conditions.
     *
     * @return LegalEntityTermsAndConditionsHistoryItem
     */
    public function declineTermsAndConditionsById(string $tncId): LegalEntityTermsAndConditionsHistoryItem;
}
