<?php

/*
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

namespace Apigee\Edge\Api\Specstore\Denormalizer;

use Apigee\Edge\Api\Specstore\Entity\Folder;
use Apigee\Edge\Api\Specstore\Entity\Spec;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class CredentialProductDenormalizer to help process response from the
 * specstore apis which could be a Folder or a Spec
 */
class CollectionDenormalizer implements DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return 'Collection' == $data->kind && !empty($data->self);
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $collection = [];
        foreach ($data->contents as $obj) {
            if ('Folder' == $obj->kind) {
                $collection[] = new Folder((array) $obj);
            }
            if ('Doc' == $obj->kind) {
                $collection[] = new Spec((array) $obj);
            }
        }

        return $collection;
    }
}
