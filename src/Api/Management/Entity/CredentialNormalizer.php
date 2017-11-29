<?php

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\EntityNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class CredentialNormalizer.
 *
 * @package Apigee\Edge\Api\Management\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class CredentialNormalizer extends EntityNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof AppCredentialInterface;
    }
}
