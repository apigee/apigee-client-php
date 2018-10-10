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

use Apigee\Edge\Api\Monetization\Entity\TermsAndConditionsInterface;
use Apigee\Edge\Api\Monetization\Structure\AcceptedTermsAndConditions;
use Apigee\Edge\Controller\OrganizationAwareControllerInterface;

/**
 * Interface AcceptedTermsAndConditionsControllerInterface.
 *
 * @see https://apidocs.apigee.com/api-reference/content/monetization-apis#terms-and-conditions
 * @see https://docs.apigee.com/api-platform/monetization/specify-terms-and-conditions#getacceptedtcapi
 */
interface AcceptedTermsAndConditionsControllerInterface extends OrganizationAwareControllerInterface
{
    /**
     * Gets all accepted terms and conditions by a developer -or company.
     *
     * @return \Apigee\Edge\Api\Monetization\Structure\AcceptedTermsAndConditions[]
     *
     * TODO What about the current query parameter?
     * https://github.com/apigee/edge-php-sdk/blob/master/Apigee/Mint/TermAndCondition.php#L139
     */
    public function getAcceptedTermsAndConditions(): array;

    /**
     * Accepts a terms and conditions.
     *
     * @param \Apigee\Edge\Api\Monetization\Entity\TermsAndConditionsInterface $tnc
     *   Only the id of the terms and conditions would not be enough because we
     *   need the organization's current timezone to be able to fix the audit
     *   date before it gets sent.
     * @param \DateTimeImmutable $auditDate
     *
     * @return \Apigee\Edge\Api\Monetization\Structure\AcceptedTermsAndConditions
     */
    public function acceptTermsAndConditions(TermsAndConditionsInterface $tnc, \DateTimeImmutable $auditDate): AcceptedTermsAndConditions;
}
