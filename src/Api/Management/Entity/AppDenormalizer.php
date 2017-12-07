<?php

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\EntityDenormalizer;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class AppDenormalizer.
 *
 * @package Apigee\Edge\Api\Management\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class AppDenormalizer extends EntityDenormalizer implements DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data['developerId'])) {
            return parent::denormalize($data, DeveloperApp::class);
        } elseif (isset($data['companyName'])) {
            return parent::denormalize($data, CompanyApp::class);
        }
        throw new NotNormalizableValueException(
            'Unable to denormalize data because both "developerId" and "companyName" are missing from data.'
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === AppInterface::class || $type instanceof AppInterface;
    }
}
