<?php

namespace Apigee\Edge\Api\Monetization\NameConverter;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * Converts "developer" to "company" on those accepted rate plans that
 * belongs to a company. This is only used in the
 * CompanyEligibleRatePlanController.
 *
 * @see \Apigee\Edge\Api\Monetization\Entity\CompanyEligibleRatePlan
 */
class CompanyEligibleRatePlanNameConverter extends NameConverterBase implements NameConverterInterface
{
    /**
     * {@inheritdoc}
     */
    protected function getExternalToLocalMapping(): array
    {
        return [
                'developer' => 'company',
            ];
    }
}
