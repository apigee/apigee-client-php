<?php

namespace Apigee\Edge\Api\Monetization\Denormalizer;

use Apigee\Edge\Api\Monetization\Entity\CompanyEligibleRatePlan;
use Apigee\Edge\Api\Monetization\NameConverter\CompanyEligibleRatePlanNameConverter;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class CompanyEligibleRatePlanDenormalizer extends AcceptedRatePlanDenormalizer
{
    /**
     * Fully qualified class name of the company accepted rate plan entity.
     *
     * @var string
     */
    protected $companyEligibleRatePlanClass = CompanyEligibleRatePlan::class;

    /**
     * CompanyEligibleRatePlanDenormalizer constructor.
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
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        return parent::denormalize($data, $this->companyEligibleRatePlanClass, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        if (parent::supportsDenormalization($data, $type, $format)) {
            return $data->developer->isCompany;
        }

        return false;
    }
}
