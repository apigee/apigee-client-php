<?php

namespace Apigee\Edge\Structure;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class CredentialProductDenormalizer.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class CredentialProductDenormalizer implements DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type instanceof CredentialProductInterface;
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return new CredentialProduct($data->apiproduct, $data->status);
    }
}
