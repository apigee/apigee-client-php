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

namespace Apigee\Edge\Api\Docstore\Denormalizer;

use Apigee\Edge\Api\Docstore\Entity\Collection;
use Apigee\Edge\Api\Docstore\Entity\Doc;
use Apigee\Edge\Api\Docstore\Entity\Folder;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class DocstoreDenormalizer to help process response from the
 * Docstore apis which could be a Folder or a Spec.
 */
class DocstoreDenormalizer implements DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return 'Collection' == $data->kind && !empty($data->self);
    }

    /**
     * @return Collection
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $collection = [];
        foreach ($data->contents as $obj) {
            if ('Folder' == $obj->kind) {
                $collection[] = new Folder((array) $obj);
            }
            if ('Doc' == $obj->kind) {
                $collection[] = new Doc((array) $obj);
            }
        }

        return new Collection(['contents' => $collection]);
    }
}
