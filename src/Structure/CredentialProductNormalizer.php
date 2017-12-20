<?php

namespace Apigee\Edge\Structure;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class CredentialProductNormalizer.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class CredentialProductNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     *
     * @param \Apigee\Edge\Structure\CredentialProductInterface $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return (object) [
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
