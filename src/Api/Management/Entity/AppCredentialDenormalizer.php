<?php

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\EntityDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class AppCredentialDenormalizer.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class AppCredentialDenormalizer extends EntityDenormalizer implements DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return in_array(AppCredential::class, class_parents($type));
    }
}
