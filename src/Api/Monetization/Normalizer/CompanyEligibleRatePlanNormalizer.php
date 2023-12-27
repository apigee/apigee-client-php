<?php

namespace Apigee\Edge\Api\Monetization\Normalizer;

use Apigee\Edge\Api\Monetization\Entity\CompanyEligibleRatePlanInterface;
use Apigee\Edge\Api\Monetization\NameConverter\CompanyEligibleRatePlanNameConverter;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class CompanyEligibleRatePlanNormalizer extends AcceptedRatePlanNormalizer
{
    /**
     * CompanyEligibleRatePlanNormalizer constructor.
     *
     * @param \Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface|null $classMetadataFactory
     * @param \Symfony\Component\Serializer\NameConverter\NameConverterInterface|null $nameConverter
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface|null $propertyAccessor
     * @param \Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface|null $propertyTypeExtractor
     */
    public function __construct(?ClassMetadataFactoryInterface $classMetadataFactory = null, ?NameConverterInterface $nameConverter = null, ?PropertyAccessorInterface $propertyAccessor = null, ?PropertyTypeExtractorInterface $propertyTypeExtractor = null)
    {
        $nameConverter = $nameConverter ?? new CompanyEligibleRatePlanNameConverter();
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof CompanyEligibleRatePlanInterface;
    }
}
