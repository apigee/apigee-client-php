<?php

namespace Apigee\Edge\Structure;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\scalar;

/**
 * Class CredentialProductNormalizer.
 *
 * @package Apigee\Edge\Structure
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class CredentialProductNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return new CredentialProduct($data['apiproduct'], $data['status']);
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type instanceof CredentialProductInterface;
    }

    /**
     * @inheritdoc
     *
     * @param \Apigee\Edge\Structure\CredentialProductInterface $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'apiproduct' => $object->getApiproduct(),
            'status' => $object->getStatus(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof CredentialProductInterface;
    }
}
