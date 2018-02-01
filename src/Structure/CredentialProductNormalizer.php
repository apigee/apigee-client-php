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
     * @psalm-suppress InvalidReturnType Returning an object here is required
     * for creating a valid Apigee Edge request.
     */
    public function normalize($object, $format = null, array $context = [])
    {
        /* @var \Apigee\Edge\Structure\CredentialProductInterface $object */
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
