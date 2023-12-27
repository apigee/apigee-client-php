<?php

namespace Apigee\Edge\Api\Monetization\Entity;

use Apigee\Edge\Api\Monetization\Entity\Property\CompanyPropertyAwareTrait;

/**
 * Represents an accepted rate plan by a company.
 */
class CompanyEligibleRatePlan extends AcceptedRatePlan implements CompanyEligibleRatePlanInterface
{
    use CompanyPropertyAwareTrait;
}