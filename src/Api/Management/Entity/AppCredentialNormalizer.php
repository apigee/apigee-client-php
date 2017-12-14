<?php

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\EntityNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class AppCredentialNormalizer.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class AppCredentialNormalizer extends EntityNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof AppCredentialInterface;
    }
}
