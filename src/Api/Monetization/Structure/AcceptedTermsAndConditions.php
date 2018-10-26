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

namespace Apigee\Edge\Api\Monetization\Structure;

use Apigee\Edge\Api\Monetization\Entity\Property\IdPropertyAwareTrait;
use Apigee\Edge\Api\Monetization\Entity\Property\IdPropertyInterface;
use Apigee\Edge\Api\Monetization\Entity\TermsAndConditionsInterface;
use Apigee\Edge\Structure\BaseObject;

/**
 * Represents an accepted terms and conditions by a developer- or company.
 */
final class AcceptedTermsAndConditions extends BaseObject implements IdPropertyInterface
{
    use IdPropertyAwareTrait;

    public const ACTION_ACCEPTED = 'ACCEPTED';

    public const ACTION_DECLINED = 'DECLINED';

    /** @var string */
    private $action;

    /**
     * According to Apigee Edge engineers the timezone of the audit date is
     * always UTC and it is always the current time on the server
     * no matter what is being sent in the payload - even if the
     * auditDate is a required parameter at this moment.
     *
     * @var \DateTimeImmutable
     */
    private $auditDate;

    /** @var \Apigee\Edge\Api\Monetization\Entity\TermsAndConditions */
    private $tnc;

    /**
     * @return \DateTimeImmutable
     */
    public function getAuditDate(): \DateTimeImmutable
    {
        return $this->auditDate;
    }

    /**
     * @param \DateTimeImmutable $auditDate
     */
    public function setAuditDate(\DateTimeImmutable $auditDate): void
    {
        $this->auditDate = $auditDate;
    }

    /**
     * @return \Apigee\Edge\Api\Monetization\Entity\TermsAndConditionsInterface
     */
    public function getTnc(): TermsAndConditionsInterface
    {
        return $this->tnc;
    }

    /**
     * @param \Apigee\Edge\Api\Monetization\Entity\TermsAndConditionsInterface $tnc
     */
    public function setTnc(TermsAndConditionsInterface $tnc): void
    {
        $this->tnc = $tnc;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }
}
