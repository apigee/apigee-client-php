<?php

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\EntityDenormalizer;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class AppDenormalizer.
 *
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
        throw new UnexpectedValueException(
            'Unable to denormalize data because both "developerId" and "companyName" are missing from data.'
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return AppInterface::class === $type || $type instanceof AppInterface;
    }
}
