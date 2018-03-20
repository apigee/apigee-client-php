<?php

/**
 * Copyright 2018 Google LLC
 *                            
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *                            
 *      https://www.apache.org/licenses/LICENSE-2.0
 *                            
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\EntityDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class AppCredentialDenormalizer.
 */
class AppCredentialDenormalizer extends EntityDenormalizer implements DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return $type instanceof AppCredentialInterface || in_array(AppCredentialInterface::class, class_implements($type));
    }

    /**
     * @inheritdoc
     */
    protected function denormalizeObjectProperty(
        bool $isCollection,
        $data,
        string $class,
        string $format = null,
        array $context = []
    ) {
        if (\DateTimeImmutable::class == $class && $data === -1) {
            // Taking special care of -1 as a credential's expiresAt property value.
            // Currently we does not limit this workaround to only the mentioned field because as far as we know
            // only expiresAt could have -1 as its value.
            // @see \Apigee\Edge\Api\Management\Entity\AppCredential::$expiresAt
            return null;
        }

        return parent::denormalizeObjectProperty(
            $isCollection,
            $data,
            $class,
            $format,
            $context
        );
    }
}
